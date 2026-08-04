<?php

namespace App\Http\Controllers;

use App\Http\Requests\Board\DestroyThreadRequest;
use App\Http\Requests\Board\StoreThreadRequest;
use App\Http\Requests\Board\UpdateThreadRequest;
use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\Territory\Location;
use App\Services\Board\PostComposerViewData;
use App\Services\Board\PostDraftService;
use App\Services\Board\ThreadReadService;
use App\Services\Board\ThreadWriter;
use App\Services\Economy\TransferReversalService;
use App\Services\PermissionService;
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
        private TransferReversalService $transferReversals,
        private ThreadReadService $reads,
        private PostDraftService $drafts,
        private PostComposerViewData $composer,
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

        if ($request->user()) {
            return redirect()->route('draft.topic', $board ?: []);
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

    public function view(Request $request, ForumThread $thread, int|string $page = 1): View|RedirectResponse
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
            'posts.elements.message',
            'posts.elements.transfer.items.item',
            'posts.elements.transfer.actor',
            'posts.elements.transfer.recipient',
            'posts.elements.transfer.reversal',
            'posts.elements.transfer.reversalOf',
            'posts.elements.transfer.sender',
            'posts.elements.sceneTransition.endedScene.location',
            'posts.elements.sceneTransition.startedScene.location',
            'posts.elements.poll.options.participations.user',
            'posts.elements.poll.participations',
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
            'posts.elements.message',
            'posts.elements.transfer.items.item',
            'posts.elements.transfer.actor',
            'posts.elements.transfer.recipient',
            'posts.elements.transfer.reversal',
            'posts.elements.transfer.reversalOf',
            'posts.elements.transfer.sender',
            'posts.elements.sceneTransition.endedScene.location',
            'posts.elements.sceneTransition.startedScene.location',
            'posts.elements.poll.options.participations.user',
            'posts.elements.poll.participations',
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
        $characters = auth()->check()
            ? auth()->user()->characters()->orderBy('name')->get()
            : collect();
        $reversibleTransferIds = auth()->check()
            ? $thread->posts
                ->flatMap->transfers
                ->filter(fn ($transfer) => $this->transferReversals->canReverse($transfer, auth()->user()))
                ->pluck('id')
            : collect();
        $canCreatePost = auth()->check() && auth()->user()->can('create', [Post::class, $thread]);
        $composerData = null;
        if ($canCreatePost && $characters->isNotEmpty()) {
            $draft = $this->drafts->replyState(auth()->user(), $thread);
            $draft->setRelation('thread', $thread);
            $composerData = $this->composer->make($draft, auth()->user(), $thread);
        }

        $response = view('thread.view', [
            'canCreatePost' => $canCreatePost,
            'canDeleteThread' => auth()->check() && auth()->user()->can('delete', $thread),
            'canEditThread' => auth()->check() && auth()->user()->can('update', $thread),
            'canViewSubscribers' => auth()->check() && $this->permissionService->allows('viewthreadsubscriptions', $thread, auth()->user()),
            'characters' => $characters,
            'composerData' => $composerData,
            'posts' => $posts,
            'reversibleTransferIds' => $reversibleTransferIds,
            'thread' => $thread,
            'subscription' => auth()->check() ? $thread->subscriptions()->where('user_id', auth()->id())->first() : null,
            'subscriberCount' => $thread->subscriptions()->count(),
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
            'thread' => $thread->load(['board', 'firstPost.character', 'firstPost.elements.message']),
        ]);
    }

    public function destroy(DestroyThreadRequest $request, ForumThread $thread): RedirectResponse
    {
        $this->threads->delete($thread);

        return redirect()->route('board');
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
