<?php

namespace App\Http\Controllers;

use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\Character;
use App\Services\Board\ForumCounters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ThreadController extends Controller
{
    private const PAGE_ENTRIES = 20;

    public function create(Request $request, ?Board $board = null): View|RedirectResponse
    {
        $this->requireUser();
        $this->authorize('create', ForumThread::class);

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'board' => ['required', 'integer', 'exists:boards,id'],
                'character' => ['required', 'integer'],
                'name' => ['required', 'string', 'max:225'],
                'message' => ['required', 'string'],
                'important' => ['nullable', 'boolean'],
                'smilies' => ['nullable', 'boolean'],
                'signature' => ['nullable', 'boolean'],
            ]);

            $board = Board::findOrFail((int) $data['board']);
            $this->authorize('createThread', $board);

            $character = $this->userCharacter((int) $data['character']);
            $counters = app(ForumCounters::class);
            $canMarkAsImportant = $request->user()->can('markAsImportant', new ForumThread(['board_id' => $board->id]));

            $thread = DB::transaction(function () use ($request, $board, $character, $data, $counters, $canMarkAsImportant) {
                $time = now()->timestamp;

                $thread = ForumThread::create([
                    'board_id' => $board->id,
                    'name' => trim($data['name']),
                    'first_post_at' => $time,
                    'post_count' => 1,
                    'last_post_at' => $time,
                    'views' => 0,
                    'important' => $canMarkAsImportant ? (int) $request->boolean('important') : 0,
                ]);

                $post = Post::create([
                    'board_id' => $board->id,
                    'thread_id' => $thread->id,
                    'user_id' => $request->user()->id,
                    'character_id' => $character->id,
                    'time' => $time,
                    'message' => trim($data['message']),
                    'smilies' => (int) $request->boolean('smilies'),
                    'signature' => (int) $request->boolean('signature'),
                    'ip' => $request->ip(),
                ]);

                $thread->update([
                    'first_post_id' => $post->id,
                    'last_post_id' => $post->id,
                ]);

                $counters->refreshThread($thread);
                $counters->refreshBoard($board);
                $counters->refreshUser($request->user()->id);
                $counters->refreshCharacter($character->id);

                return $thread;
            });

            return redirect()->route('thread.view', ['thread' => $thread->id]);
        }

        return view('thread.create', [
            'boards' => $this->threadBoardOptions(),
            'characters' => auth()->user()->characters()->orderBy('name')->get(),
            'canMarkAsImportant' => auth()->user()->can('markAsImportant', new ForumThread(['board_id' => $board?->id ?? 0])),
            'selectedBoard' => $board,
        ]);
    }

    public function view(Request $request, ForumThread $thread, int|string $page = 1): View
    {
        $this->authorize('view', $thread);
        $viewedThreads = session('viewed.1', []);

        $thread->load([
            'board.parent',
            'posts.author',
            'posts.character',
            'posts.transfers.items.item',
            'posts.transfers.recipient',
            'posts.transfers.sender',
        ]);
        $thread->increment('views');
        $thread->refresh()->load([
            'board.parent',
            'posts.author',
            'posts.character',
            'posts.transfers.items.item',
            'posts.transfers.recipient',
            'posts.transfers.sender',
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
            'canTransfer' => auth()->check() && $this->permissionService->allows('transfer', $thread, auth()->user()),
            'characters' => auth()->check() ? auth()->user()->characters()->with('inventory.item')->orderBy('name')->get() : collect(),
            'posts' => $posts,
            'quotedMessage' => $quotedPost ? $this->quoteText($quotedPost) : '',
            'thread' => $thread,
            'viewedThreads' => $viewedThreads,
        ]);

        if (auth()->check()) {
            session()->put('viewed.1.' . $thread->id, $thread->getRawOriginal('last_post_at'));
        }

        return $response;
    }

    public function edit(Request $request, ForumThread $thread): View|RedirectResponse
    {
        $this->authorize('update', $thread);

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'board' => ['required', 'integer', 'exists:boards,id'],
                'name' => ['required', 'string', 'max:225'],
                'important' => ['nullable', 'boolean'],
            ]);

            $oldBoard = $thread->board;
            $newBoard = Board::findOrFail((int) $data['board']);
            $this->authorize('createThread', $newBoard);

            $counters = app(ForumCounters::class);
            $canMarkAsImportant = $request->user()->can('markAsImportant', $thread);

            DB::transaction(function () use ($request, $thread, $oldBoard, $newBoard, $data, $counters, $canMarkAsImportant) {
                $thread->update([
                    'board_id' => $newBoard->id,
                    'name' => trim($data['name']),
                    'important' => $canMarkAsImportant ? (int) $request->boolean('important') : $thread->important,
                ]);

                if (! $oldBoard || $oldBoard->id !== $newBoard->id) {
                    Post::where('thread_id', $thread->id)->update(['board_id' => $newBoard->id]);
                    $counters->refreshBoard($oldBoard);
                }

                $counters->refreshThread($thread);
                $counters->refreshBoard($newBoard);
            });

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

    public function destroy(Request $request, ForumThread $thread): RedirectResponse
    {
        $this->authorize('delete', $thread);
        $request->validate(['delete' => ['required', 'accepted']]);

        $board = $thread->board;
        $userIds = $thread->posts()->pluck('user_id');
        $characterIds = $thread->posts()->pluck('character_id');
        $counters = app(ForumCounters::class);

        DB::transaction(function () use ($thread, $board, $userIds, $characterIds, $counters) {
            Post::where('thread_id', $thread->id)->delete();
            $thread->delete();

            $counters->refreshBoard($board);
            $counters->refreshUsers($userIds);
            $counters->refreshCharacters($characterIds);
        });

        return redirect()->route('board');
    }

    private function requireUser(): void
    {
        abort_unless(auth()->check(), 403);
    }

    private function userCharacter(int $characterId): Character
    {
        return auth()->user()
            ->characters()
            ->whereKey($characterId)
            ->firstOrFail();
    }

    private function quoteText(Post $post): string
    {
        $author = $post->character?->name
            ?? $post->author?->name
            ?? 'Unbekannter Charakter';
        $author = str_replace(']', ')', $author);

        return '[q=' . $author . ']' . trim($post->message) . '[/q]' . PHP_EOL;
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
