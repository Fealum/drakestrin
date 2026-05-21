<?php

namespace App\Http\Controllers;

use App\Http\Requests\Board\DestroyThreadRequest;
use App\Http\Requests\Board\StoreThreadRequest;
use App\Http\Requests\Board\UpdateThreadRequest;
use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\Territory\Location;
use App\Services\Board\ThreadWriter;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ThreadController extends Controller
{
    private const PAGE_ENTRIES = 20;

    public function __construct(PermissionService $permissionService, private ThreadWriter $threads)
    {
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
        $viewedThreads = session('viewed.1', []);

        $thread->load([
            'board.parent',
            'currentScene.location',
            'posts.author',
            'posts.character',
            'posts.transfers.items.item',
            'posts.transfers.recipient',
            'posts.transfers.sender',
            'scenes.location',
        ]);
        $thread->increment('views');
        $thread->refresh()->load([
            'board.parent',
            'currentScene.location',
            'posts.author',
            'posts.character',
            'posts.transfers.items.item',
            'posts.transfers.recipient',
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
        $quotedPost = $request->filled('quote')
            ? $thread->posts->firstWhere('id', (int) $request->query('quote'))
            : null;

        $response = view('thread.view', [
            'canCreatePost' => auth()->check() && auth()->user()->can('create', [Post::class, $thread]),
            'canCreateCharacter' => auth()->check() && $this->permissionService->allows('createcharacter', $thread, auth()->user()),
            'canDeleteThread' => auth()->check() && auth()->user()->can('delete', $thread),
            'canEditThread' => auth()->check() && auth()->user()->can('update', $thread),
            'canEndScene' => auth()->check() && auth()->user()->can('endScene', $thread),
            'canSetScene' => auth()->check() && auth()->user()->can('setScene', $thread),
            'canTransfer' => auth()->check() && $this->permissionService->allows('transfer', $thread, auth()->user()),
            'characters' => auth()->check() ? auth()->user()->characters()->with('inventory.item')->orderBy('name')->get() : collect(),
            'posts' => $posts,
            'quotedMessage' => $quotedPost ? $this->quoteText($quotedPost) : '',
            'thread' => $thread,
            'timelineEntries' => $this->timelineEntries($posts->getCollection(), $thread->scenes),
            'viewedThreads' => $viewedThreads,
        ]);

        if (auth()->check()) {
            session()->put('viewed.1.' . $thread->id, $thread->getRawOriginal('last_post_at'));
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

        return '[q=' . $author . ']' . trim($post->message) . '[/q]' . PHP_EOL;
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
