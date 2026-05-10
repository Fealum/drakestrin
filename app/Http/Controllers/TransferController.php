<?php

namespace App\Http\Controllers;

use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\Character;
use App\Models\Inventory;
use App\Models\Transfer;
use App\Models\TransferItem;
use App\Services\Board\ForumCounters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function transfer(Request $request, ForumThread $thread): RedirectResponse
    {
        abort_unless(auth()->check(), 403);
        abort_unless($this->permissionService->allows('transfer', $thread, $request->user()), 403);

        $data = $request->validate([
            'character' => ['required', 'integer', 'exists:dra_character,id'],
            'inventory' => ['required', 'array', 'min:1'],
            'inventory.*' => ['integer', 'exists:dra_inventory,id'],
            'inventorystack' => ['nullable', 'array'],
            'inventorystack.*' => ['nullable', 'string', 'max:40'],
            'recipient' => ['required', 'integer', 'exists:dra_character,id'],
        ]);

        $sender = Character::query()
            ->whereKey((int) $data['character'])
            ->where('user', $request->user()->id)
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
            ->with('itemModel')
            ->whereIn('id', $selectedInventoryIds)
            ->where('table__owner', 6)
            ->where('owner', $sender->id)
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
                'thread' => $thread->id,
                'board' => $thread->board,
                'user' => 2,
                'character' => 3,
                'time' => $time,
                'message' => '',
                'smilies' => 0,
                'signature' => 0,
                'ip' => $request->ip(),
            ]);

            $transfer = Transfer::create([
                'post' => $actionPost->id,
                'table__sender' => 6,
                'sender' => $sender->id,
                'table__recipient' => 6,
                'recipient' => $recipient->id,
            ]);

            foreach ($selectedInventoryIds as $inventoryId) {
                $inventory = $inventories->get($inventoryId);

                if (! $inventory || ! $inventory->itemModel) {
                    continue;
                }

                [$itemId, $stack] = $this->moveInventory($inventory, $recipient, $data['inventorystack'][$inventoryId] ?? null);

                TransferItem::create([
                    'transfer' => $transfer->id,
                    'item' => $itemId,
                    'stack' => $stack,
                ]);
            }

            $thread->post__last = $actionPost->id;
            $thread->post__last_time = $time;
            $thread->save();

            $counters->refreshThread($thread);
            $counters->refreshBoard($thread->boardModel);
            $counters->refreshUser(2);
            $counters->refreshCharacter(3);

            return $actionPost;
        });

        return redirect(route('thread.view', ['thread' => $thread->id, 'page' => 'last']) . '#post' . $post->id);
    }

    /**
     * @return array{0:int,1:int}
     */
    private function moveInventory(Inventory $inventory, Character $recipient, int|string|null $requestedStack): array
    {
        $item = $inventory->itemModel;

        if (! $item->stackable) {
            $inventory->update([
                'table__owner' => 6,
                'owner' => $recipient->id,
                'wear' => 0,
            ]);

            return [$item->id, 0];
        }

        $stack = $inventory->undounitary($requestedStack ?? 1);

        if ($stack <= 0) {
            $stack = 1;
        } elseif ($stack > $inventory->stack) {
            $stack = $inventory->stack;
        }

        $recipientInventory = Inventory::query()
            ->where('item', $item->id)
            ->where('table__owner', 6)
            ->where('owner', $recipient->id)
            ->where('wear', 0)
            ->first();

        if ($stack === $inventory->stack && ! $recipientInventory) {
            $inventory->update([
                'table__owner' => 6,
                'owner' => $recipient->id,
                'wear' => 0,
            ]);
        } elseif ($stack === $inventory->stack && $recipientInventory) {
            $recipientInventory->increment('stack', $stack);
            $inventory->delete();
        } else {
            if ($recipientInventory) {
                $recipientInventory->increment('stack', $stack);
            } else {
                Inventory::create([
                    'item' => $item->id,
                    'stack' => $stack,
                    'wear' => 0,
                    'table__owner' => 6,
                    'owner' => $recipient->id,
                    'timelastvalue' => 0,
                    'data' => '',
                ]);
            }

            $inventory->decrement('stack', $stack);
        }

        return [$item->id, $stack];
    }
}
