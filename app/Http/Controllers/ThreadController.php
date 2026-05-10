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
                'board' => ['required', 'integer', 'exists:dra_board,id'],
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
            $canMarkAsImportant = $request->user()->can('markAsImportant', new ForumThread(['board' => $board->id]));

            $thread = DB::transaction(function () use ($request, $board, $character, $data, $counters, $canMarkAsImportant) {
                $time = now()->timestamp;

                $thread = ForumThread::create([
                    'board' => $board->id,
                    'name' => trim($data['name']),
                    'post__first_time' => $time,
                    'post__total' => 1,
                    'post__last_time' => $time,
                    'views' => 0,
                    'important' => $canMarkAsImportant ? (int) $request->boolean('important') : 0,
                ]);

                $post = Post::create([
                    'board' => $board->id,
                    'thread' => $thread->id,
                    'user' => $request->user()->id,
                    'character' => $character->id,
                    'time' => $time,
                    'message' => trim($data['message']),
                    'smilies' => (int) $request->boolean('smilies'),
                    'signature' => (int) $request->boolean('signature'),
                    'ip' => $request->ip(),
                ]);

                $thread->update([
                    'post__first' => $post->id,
                    'post__last' => $post->id,
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
            'canMarkAsImportant' => auth()->user()->can('markAsImportant', new ForumThread(['board' => $board?->id ?? 0])),
            'selectedBoard' => $board,
        ]);
    }

    public function view(Request $request, ForumThread $thread, int|string $page = 1): View
    {
        $this->authorize('view', $thread);
        $viewedThreads = session('viewed.1', []);

        $thread->load([
            'boardModel.parentBoard',
            'posts.author',
            'posts.characterModel',
            'posts.transfers.items.itemModel',
            'posts.transfers.recipientCharacter',
            'posts.transfers.senderCharacter',
        ]);
        $thread->increment('views');
        $thread->refresh()->load([
            'boardModel.parentBoard',
            'posts.author',
            'posts.characterModel',
            'posts.transfers.items.itemModel',
            'posts.transfers.recipientCharacter',
            'posts.transfers.senderCharacter',
        ]);

        $this->setLocation($thread);

        if ($page === 'last') {
            $page = (int) ceil(max($thread->post__total, 1) / self::PAGE_ENTRIES);
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
            'characters' => auth()->check() ? auth()->user()->characters()->with('inventory.itemModel')->orderBy('name')->get() : collect(),
            'posts' => $posts,
            'quotedMessage' => $quotedPost ? $this->quoteText($quotedPost) : '',
            'thread' => $thread,
            'viewedThreads' => $viewedThreads,
        ]);

        if (auth()->check()) {
            session()->put('viewed.1.' . $thread->id, $thread->getRawOriginal('post__last_time'));
        }

        return $response;
    }

    public function edit(Request $request, ForumThread $thread): View|RedirectResponse
    {
        $this->authorize('update', $thread);

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'board' => ['required', 'integer', 'exists:dra_board,id'],
                'name' => ['required', 'string', 'max:225'],
                'important' => ['nullable', 'boolean'],
            ]);

            $oldBoard = $thread->boardModel;
            $newBoard = Board::findOrFail((int) $data['board']);
            $this->authorize('createThread', $newBoard);

            $counters = app(ForumCounters::class);
            $canMarkAsImportant = $request->user()->can('markAsImportant', $thread);

            DB::transaction(function () use ($request, $thread, $oldBoard, $newBoard, $data, $counters, $canMarkAsImportant) {
                $thread->update([
                    'board' => $newBoard->id,
                    'name' => trim($data['name']),
                    'important' => $canMarkAsImportant ? (int) $request->boolean('important') : $thread->important,
                ]);

                if (! $oldBoard || $oldBoard->id !== $newBoard->id) {
                    Post::where('thread', $thread->id)->update(['board' => $newBoard->id]);
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
            'thread' => $thread->load('boardModel'),
        ]);
    }

    public function delete(ForumThread $thread): View
    {
        $this->authorize('delete', $thread);

        return view('thread.delete', [
            'postCount' => $thread->posts()->count(),
            'thread' => $thread->load(['boardModel', 'firstPost.characterModel']),
        ]);
    }

    public function destroy(Request $request, ForumThread $thread): RedirectResponse
    {
        $this->authorize('delete', $thread);
        $request->validate(['delete' => ['required', 'accepted']]);

        $board = $thread->boardModel;
        $userIds = $thread->posts()->pluck('user');
        $characterIds = $thread->posts()->pluck('character');
        $counters = app(ForumCounters::class);

        DB::transaction(function () use ($thread, $board, $userIds, $characterIds, $counters) {
            Post::where('thread', $thread->id)->delete();
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
        $author = $post->characterModel?->name
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
            ->groupBy('board');

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
