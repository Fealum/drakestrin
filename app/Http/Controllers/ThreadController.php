<?php

namespace App\Http\Controllers;

use App\Data\Economy\InventoryOwner;
use App\Http\Requests\Board\DestroyThreadRequest;
use App\Http\Requests\Board\StoreThreadRequest;
use App\Http\Requests\Board\UpdateThreadRequest;
use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\Economy\CompanySite;
use App\Models\Territory\Location;
use App\Repositories\Territory\LocationRepository;
use App\Services\Board\ThreadReadService;
use App\Services\Board\ThreadWriter;
use App\Services\Economy\InventoryTimeline;
use App\Services\Economy\TransferReversalService;
use App\Services\PermissionService;
use App\Support\CompanyRepresentativeRole;
use App\Support\InventoryStockState;
use App\Support\PermissionEntityType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ThreadController extends Controller
{
    private const PAGE_ENTRIES = 20;

    public function __construct(
        PermissionService $permissionService,
        private ThreadWriter $threads,
        private InventoryTimeline $inventoryTimeline,
        private TransferReversalService $transferReversals,
        private LocationRepository $locations,
        private ThreadReadService $reads,
    ) {
        parent::__construct($permissionService);
    }

    public function create(StoreThreadRequest $request, ?Board $board = null): View|RedirectResponse
    {
        if ($request->isMethod('post')) {
            $data = $request->toData();
            $board = Board::findOrFail($data->boardId);
            $this->authorize('createThread', $board);

            $canMarkAsImportant = $request->user()->can('markAsImportant', new ForumThread(['board_id' => $board->id]));
            $thread = $this->threads->create($board, $request->user(), $data, $canMarkAsImportant, $request->ip());

            return redirect()->route('thread.view', ['thread' => $thread->id]);
        }

        return view('thread.create', [
            'boards' => $this->threadBoardOptions(),
            'characters' => auth()->user()->characters()->orderBy('name')->get(),
            'canMarkAsImportant' => auth()->user()->can('markAsImportant', new ForumThread(['board_id' => $board?->id ?? 0])),
            'canSetScene' => auth()->user()->can('setScene', new ForumThread(['board_id' => $board?->id ?? 0])),
            'locations' => Location::query()->orderBy('priority')->orderByRaw('LOWER(name)')->get(),
            'selectedBoard' => $board,
        ]);
    }

    public function view(Request $request, ForumThread $thread, int|string $page = 1): View
    {
        $this->authorize('view', $thread);
        $thread->load([
            'board.parent',
            'currentScene.location.inventory.item',
            'posts.author',
            'posts.character',
            'posts.transfers.items.item',
            'posts.transfers.actor',
            'posts.transfers.recipient',
            'posts.transfers.reversal',
            'posts.transfers.reversalOf',
            'posts.transfers.sender',
            'scenes.location',
        ]);
        $thread->increment('views');
        $thread->refresh()->load([
            'board.parent',
            'currentScene.location.inventory.item',
            'posts.author',
            'posts.character',
            'posts.transfers.items.item',
            'posts.transfers.actor',
            'posts.transfers.recipient',
            'posts.transfers.reversal',
            'posts.transfers.reversalOf',
            'posts.transfers.sender',
            'scenes.location',
        ]);

        $this->setLocation($thread);

        if ($page === 'last') {
            $page = (int) ceil(max($thread->post_count, 1) / self::PAGE_ENTRIES);
        }

        $posts = new LengthAwarePaginator(
            $thread->posts->forPage((int) $page, self::PAGE_ENTRIES)->values(),
            $thread->posts->count(),
            self::PAGE_ENTRIES,
            max((int) $page, 1),
            ['path' => route('thread.view', ['thread' => $thread->id])]
        );
        $firstUnreadPost = auth()->check() ? $this->reads->firstUnreadPost($thread, auth()->user()) : null;
        $unreadPostIds = $firstUnreadPost
            ? $posts->getCollection()->where('id', '>=', $firstUnreadPost->id)->pluck('id')
            : collect();
        $quotedPost = $request->filled('quote')
            ? $thread->posts->firstWhere('id', (int) $request->query('quote'))
            : null;
        $characters = auth()->check()
            ? auth()->user()->characters()->with('inventory.item')->orderBy('name')->get()
            : collect();
        $storyAt = $thread->currentScene?->story_started_at;

        if ($storyAt !== null) {
            $characters->each(function ($character) use ($storyAt) {
                $character->setRelation('inventory', $this->inventoryTimeline->transferableInventory(
                    new InventoryOwner(PermissionEntityType::CHARACTER, $character->id),
                    $storyAt,
                ));
            });
        }

        $locationInventory = $storyAt !== null
            ? $this->inventoryTimeline->transferableInventory(
                new InventoryOwner(PermissionEntityType::LOCATION, $thread->currentScene->location_id),
                $storyAt,
            )
            : collect();
        $localSites = collect();

        if ($storyAt !== null && $thread->currentScene?->location) {
            $localSites = CompanySite::query()
                ->with(['company.owners.character', 'company.representatives.character', 'company.sites:id,company_id,name', 'representatives.character', 'location', 'inventory.item'])
                ->whereIn('location_id', $this->locations->ancestorLocationIds($thread->currentScene->location))
                ->orderBy('company_id')
                ->orderBy('name')
                ->get();

            $localSites->each(function (CompanySite $site) use ($storyAt) {
                $site->setRelation(
                    'inventory',
                    $this->inventoryTimeline->transferableInventory(
                        new InventoryOwner(PermissionEntityType::COMPANY_SITE, $site->id),
                        $storyAt,
                    )->filter(fn ($inventory) => (int) $inventory->wear >= InventoryStockState::RESERVED->value)->values(),
                );
            });
        }
        $representedLocalSites = $localSites
            ->filter(function (CompanySite $site) use ($characters) {
                $representativeIds = $site->company->owners
                    ->pluck('character_id')
                    ->concat($site->company->representatives->where('role', CompanyRepresentativeRole::MANAGER)->pluck('character_id'))
                    ->concat($site->representatives->pluck('character_id'))
                    ->unique();

                return $representativeIds->intersect($characters->pluck('id'))->isNotEmpty();
            })
            ->values();
        $reversibleTransferIds = auth()->check()
            ? $thread->posts
                ->flatMap->transfers
                ->filter(fn ($transfer) => $this->transferReversals->canReverse($transfer, auth()->user()))
                ->pluck('id')
            : collect();

        $response = view('thread.view', [
            'canCreatePost' => auth()->check() && auth()->user()->can('create', [Post::class, $thread]),
            'canCreateCharacter' => auth()->check() && $this->permissionService->allows('createcharacter', $thread, auth()->user()),
            'canDeleteThread' => auth()->check() && auth()->user()->can('delete', $thread),
            'canEditThread' => auth()->check() && auth()->user()->can('update', $thread),
            'canEndScene' => auth()->check() && auth()->user()->can('endScene', $thread),
            'canSetScene' => auth()->check() && auth()->user()->can('setScene', $thread),
            'canTransfer' => auth()->check() && $this->permissionService->allows('transfer', $thread, auth()->user()),
            'canViewSubscribers' => auth()->check() && $this->permissionService->allows('viewthreadsubscriptions', $thread, auth()->user()),
            'characters' => $characters,
            'posts' => $posts,
            'locationInventory' => $locationInventory,
            'localSites' => $localSites,
            'representedLocalSites' => $representedLocalSites,
            'quotedMessage' => $quotedPost ? $this->quoteText($quotedPost) : '',
            'reversibleTransferIds' => $reversibleTransferIds,
            'thread' => $thread,
            'subscription' => auth()->check() ? $thread->subscriptions()->where('user_id', auth()->id())->first() : null,
            'subscriberCount' => $thread->subscriptions()->count(),
            'timelineEntries' => $this->timelineEntries($posts->getCollection(), $thread->scenes),
            'unreadPostIds' => $unreadPostIds,
        ]);

        if (auth()->check()) {
            $this->reads->markDisplayed(auth()->user(), $thread, $posts->getCollection());
        }

        return $response;
    }

    public function edit(UpdateThreadRequest $request, ForumThread $thread): View|RedirectResponse
    {
        if ($request->isMethod('post')) {
            $data = $request->toData();
            $newBoard = Board::findOrFail($data->boardId);
            $this->authorize('createThread', $newBoard);

            $canMarkAsImportant = $request->user()->can('markAsImportant', $thread);
            $this->threads->update($thread, $newBoard, $data, $canMarkAsImportant);

            return redirect()->route('thread.view', ['thread' => $thread->id]);
        }

        return view('thread.edit', [
            'boards' => $this->threadBoardOptions(),
            'canMarkAsImportant' => auth()->user()->can('markAsImportant', $thread),
            'thread' => $thread->load('board'),
        ]);
    }

    public function delete(ForumThread $thread): View
    {
        $this->authorize('delete', $thread);

        return view('thread.delete', [
            'postCount' => $thread->posts()->count(),
            'thread' => $thread->load(['board', 'firstPost.character']),
        ]);
    }

    public function destroy(DestroyThreadRequest $request, ForumThread $thread): RedirectResponse
    {
        $this->threads->delete($thread);

        return redirect()->route('board');
    }

    private function quoteText(Post $post): string
    {
        $author = $post->character?->name
            ?? $post->author?->name
            ?? 'Unbekannter Charakter';
        $author = str_replace(']', ')', $author);

        return '[q='.$author.']'.trim($post->message).'[/q]'.PHP_EOL;
    }

    private function timelineEntries(Collection $posts, Collection $scenes): Collection
    {
        $entries = collect();

        foreach ($scenes as $scene) {
            if ($scene->starts_at_post_id === null) {
                $entries->push([
                    'type' => 'scene_start',
                    'scene' => $scene,
                    'post' => null,
                    'sort_post_id' => 0,
                    'sort' => 0,
                ]);
            }
        }

        foreach ($posts as $postNumber => $post) {
            $entries->push([
                'type' => 'post',
                'post' => $post,
                'scene' => null,
                'post_number' => $postNumber + 1,
                'sort_post_id' => $post->id,
                'sort' => 1,
            ]);

            foreach ($scenes->where('ends_at_post_id', $post->id) as $scene) {
                $entries->push([
                    'type' => 'scene_end',
                    'scene' => $scene,
                    'post' => $post,
                    'sort_post_id' => $post->id,
                    'sort' => 2,
                ]);
            }

            foreach ($scenes->where('starts_at_post_id', $post->id) as $scene) {
                $entries->push([
                    'type' => 'scene_start',
                    'scene' => $scene,
                    'post' => $post,
                    'sort_post_id' => $post->id,
                    'sort' => 3,
                ]);
            }
        }

        return $entries
            ->sortBy(fn (array $entry) => [$entry['sort_post_id'], $entry['sort'], $entry['scene']?->id ?? $entry['post']?->id ?? 0])
            ->values();
    }

    private function threadBoardOptions(): Collection
    {
        $boardsByParent = Board::query()
            ->orderBy('sort')
            ->orderByRaw('LOWER(name)')
            ->get()
            ->groupBy('parent_id');

        $walk = function (int $parentId = 0, int $level = 0) use (&$walk, $boardsByParent): Collection {
            return $boardsByParent->get($parentId, collect())
                ->filter(fn (Board $board) => Gate::allows('view', $board))
                ->flatMap(function (Board $board) use ($walk, $level) {
                    return collect([[
                        'board' => $board,
                        'disabled' => (bool) $board->cat || Gate::denies('createThread', $board),
                        'level' => $level,
                    ]])->merge($walk($board->id, $level + 1));
                });
        };

        return $walk()->values();
    }
}
