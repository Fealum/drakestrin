<?php

namespace App\Http\Controllers;

use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\User\Character;
use App\Models\Economy\Inventory;
use App\Models\Economy\Transfer;
use App\Models\Economy\TransferItem;
use App\Services\Board\ForumCounters;
use App\Services\InventoryService;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function __construct(PermissionService $permissionService, private InventoryService $inventory)
    {
        parent::__construct($permissionService);
    }

    public function transfer(Request $request, ForumThread $thread): RedirectResponse
    {
        abort_unless(auth()->check(), 403);
        abort_unless($this->permissionService->allows('transfer', $thread, $request->user()), 403);

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

        $inventories = Inventory::query()
            ->with('item')
            ->whereIn('id', $selectedInventoryIds)
            ->where('owner_type', 6)
            ->where('owner_id', $sender->id)
            ->get()
            ->keyBy('id');

        if ($inventories->isEmpty()) {
            return redirect()->route('thread.view', ['thread' => $thread->id])
                ->withErrors(['inventory' => 'Keine übertragbaren Gegenstände ausgewählt.']);
        }

        $counters = app(ForumCounters::class);
        $post = DB::transaction(function () use ($request, $thread, $sender, $recipient, $selectedInventoryIds, $inventories, $data, $counters) {
            $time = now()->timestamp;
            $actionPost = Post::create([
                'thread_id' => $thread->id,
                'board_id' => $thread->board_id,
                'user_id' => 2,
                'character_id' => 3,
                'time' => $time,
                'message' => '',
                'smilies' => 0,
                'signature' => 0,
                'ip' => $request->ip(),
            ]);

            $transfer = Transfer::create([
                'post_id' => $actionPost->id,
                'sender_type' => 6,
                'sender_id' => $sender->id,
                'recipient_type' => 6,
                'recipient_id' => $recipient->id,
            ]);

            foreach ($selectedInventoryIds as $inventoryId) {
                $inventory = $inventories->get($inventoryId);

                if (! $inventory || ! $inventory->item) {
                    continue;
                }

                [$itemId, $stack] = $this->inventory->moveInventory($inventory, 6, $recipient->id, 0, $data['inventorystack'][$inventoryId] ?? null);

                TransferItem::create([
                    'transfer_id' => $transfer->id,
                    'item_id' => $itemId,
                    'stack' => $stack,
                ]);
            }

            $thread->last_post_id = $actionPost->id;
            $thread->last_post_at = $time;
            $thread->save();

            $counters->refreshThread($thread);
            $counters->refreshBoard($thread->board);
            $counters->refreshUser(2);
            $counters->refreshCharacter(3);

            return $actionPost;
        });

        return redirect(route('thread.view', ['thread' => $thread->id, 'page' => 'last']) . '#post' . $post->id);
    }
}
