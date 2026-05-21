<?php

namespace App\Http\Controllers;

use App\Data\Economy\InventoryOwner;
use App\Data\Economy\TransferInventoryItem;
use App\Data\Economy\TransferParticipant;
use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\User\Character;
use App\Services\Board\ForumCounters;
use App\Services\Economy\TransferService;
use App\Services\PermissionService;
use App\Support\PermissionEntityType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransferController extends Controller
{
    public function __construct(PermissionService $permissionService, private TransferService $transfers)
    {
        parent::__construct($permissionService);
    }

    public function transfer(Request $request, ForumThread $thread): RedirectResponse
    {
        abort_unless(auth()->check(), 403);
        abort_unless($this->permissionService->allows('transfer', $thread, $request->user()), 403);
        abort_unless($thread->currentScene()->exists(), 403);

        $data = $request->validate([
            'character' => ['required', 'integer', 'exists:characters,id'],
            'inventory' => ['required', 'array', 'min:1'],
            'inventory.*' => ['integer', 'exists:inventories,id'],
            'inventorystack' => ['nullable', 'array'],
            'inventorystack.*' => ['nullable', 'string', 'max:40'],
            'recipient' => ['required', 'integer', 'exists:characters,id'],
        ]);

        $sender = Character::query()
            ->whereKey((int) $data['character'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        $recipient = Character::findOrFail((int) $data['recipient']);

        if ($recipient->id === $sender->id) {
            return redirect()->route('thread.view', ['thread' => $thread->id])
                ->withErrors(['recipient' => 'Sender und Empfänger müssen verschieden sein.']);
        }

        $selectedInventoryIds = collect($data['inventory'])
            ->map(fn ($inventoryId) => (int) $inventoryId)
            ->unique()
            ->values();
        $selectedItems = $selectedInventoryIds
            ->map(fn (int $inventoryId) => new TransferInventoryItem(
                inventoryId: $inventoryId,
                requestedStack: $data['inventorystack'][$inventoryId] ?? null,
            ))
            ->all();

        $counters = app(ForumCounters::class);

        try {
            $post = DB::transaction(function () use ($request, $thread, $sender, $recipient, $selectedItems, $counters) {
                $time = now()->timestamp;
                $actionPost = Post::create([
                    'thread_id' => $thread->id,
                    'board_id' => $thread->board_id,
                    'user_id' => $sender->user_id,
                    'character_id' => $sender->id,
                    'time' => $time,
                    'message' => '',
                    'smilies' => 0,
                    'signature' => 0,
                    'ip' => $request->ip(),
                ]);

                $this->transfers->transferInventories(
                    postId: $actionPost->id,
                    sender: TransferParticipant::character($sender->id),
                    recipient: TransferParticipant::character($recipient->id),
                    source: new InventoryOwner(PermissionEntityType::CHARACTER, $sender->id),
                    target: new InventoryOwner(PermissionEntityType::CHARACTER, $recipient->id),
                    items: $selectedItems,
                );

                $thread->last_post_id = $actionPost->id;
                $thread->last_post_at = $time;
                $thread->save();

                $counters->refreshThread($thread);
                $counters->refreshBoard($thread->board);
                $counters->refreshUser($sender->user_id);
                $counters->refreshCharacter($sender->id);

                return $actionPost;
            });
        } catch (InvalidArgumentException) {
            return redirect()->route('thread.view', ['thread' => $thread->id])
                ->withErrors(['inventory' => 'Keine übertragbaren Gegenstände ausgewählt.']);
        }


        return redirect(route('thread.view', ['thread' => $thread->id, 'page' => 'last']) . '#post' . $post->id);
    }
}
