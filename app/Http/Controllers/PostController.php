<?php

namespace App\Http\Controllers;

use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\Character;
use App\Services\Board\ForumCounters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PostController extends Controller
{
    private const PAGE_ENTRIES = 20;

    public function view(Post $post): RedirectResponse
    {
        return redirect($this->postUrl($post));
    }

    public function create(Request $request, ForumThread $thread): RedirectResponse
    {
        $this->requireUser();
        $this->authorize('create', [Post::class, $thread]);

        $data = $request->validate([
            'character' => ['required'],
            'message' => ['required', 'string'],
            'newcharname' => ['nullable', 'string', 'max:85'],
            'smilies' => ['nullable', 'boolean'],
            'signature' => ['nullable', 'boolean'],
        ]);

        $counters = app(ForumCounters::class);

        $post = DB::transaction(function () use ($request, $thread, $data, $counters) {
            $time = now()->timestamp;
            $character = $this->resolveCharacterForCreate($request, $thread, $data, $time);

            $post = Post::create([
                'board_id' => $thread->board_id,
                'thread_id' => $thread->id,
                'user_id' => $request->user()->id,
                'character_id' => $character->id,
                'time' => $time,
                'message' => trim($data['message']),
                'smilies' => (int) $request->boolean('smilies'),
                'signature' => (int) $request->boolean('signature'),
                'ip' => $request->ip(),
            ]);

            $counters->refreshThread($thread);
            $counters->refreshBoard($thread->board);
            $counters->refreshUser($request->user()->id);
            $counters->refreshCharacter($character->id);

            return $post;
        });

        return redirect($this->postUrl($post, 'last'));
    }

    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        return view('post.edit', [
            'characters' => auth()->user()->characters()->orderBy('name')->get(),
            'post' => $post->load(['character', 'thread.board']),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $data = $request->validate([
            'character' => ['required', 'integer'],
            'message' => ['required', 'string'],
        ]);

        $character = $this->userCharacter((int) $data['character']);
        $oldCharacterId = $post->character_id;
        $counters = app(ForumCounters::class);

        DB::transaction(function () use ($post, $character, $data, $oldCharacterId, $counters) {
            $post->update([
                'character_id' => $character->id,
                'message' => trim($data['message']),
            ]);

            if ($oldCharacterId !== $character->id) {
                $counters->refreshCharacter($oldCharacterId);
                $counters->refreshCharacter($character->id);

                if ($character->regdate && $character->regdate->timestamp > $post->time->timestamp) {
                    $character->update(['regdate' => $post->time->timestamp]);
                }
            }
        });

        return redirect($this->postUrl($post->refresh()));
    }

    public function delete(Post $post): View
    {
        $this->authorize('delete', $post);
        $post->load(['character', 'thread.board', 'author']);

        return view('post.delete', [
            'deletesThread' => $post->thread->posts()->count() === 1,
            'post' => $post,
        ]);
    }

    public function ip(Post $post): View
    {
        $this->authorize('viewIp', $post);
        $post->load(['author', 'character', 'thread']);

        $authorIps = Post::query()
            ->select('ip')
            ->selectRaw('COUNT(*) as post_count')
            ->selectRaw('MIN(time) as first_post_time')
            ->selectRaw('MAX(time) as last_post_time')
            ->where('user_id', $post->user_id)
            ->whereNotNull('ip')
            ->where('ip', '<>', '')
            ->groupBy('ip')
            ->orderByDesc('post_count')
            ->orderByDesc('last_post_time')
            ->get();

        $sameIpUsers = Post::query()
            ->select('user_id')
            ->selectRaw('COUNT(*) as post_count')
            ->selectRaw('MIN(time) as first_post_time')
            ->selectRaw('MAX(time) as last_post_time')
            ->with('author')
            ->where('ip', $post->ip)
            ->whereNotNull('ip')
            ->where('ip', '<>', '')
            ->groupBy('user_id')
            ->orderByDesc('post_count')
            ->orderByDesc('last_post_time')
            ->get();

        return view('post.ip', [
            'authorIps' => $authorIps,
            'post' => $post,
            'sameIpUsers' => $sameIpUsers,
        ]);
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);
        $request->validate(['delete' => ['required', 'accepted']]);

        $thread = $post->thread;
        $board = $post->board;
        $userId = $post->user_id;
        $characterId = $post->character_id;
        $counters = app(ForumCounters::class);
        $deletesThread = $thread->posts()->count() === 1;

        DB::transaction(function () use ($post, $thread, $board, $userId, $characterId, $counters, $deletesThread) {
            $post->delete();

            if ($deletesThread) {
                $thread->delete();
            } else {
                $counters->refreshThread($thread);
            }

            $counters->refreshBoard($board);
            $counters->refreshUser($userId);
            $counters->refreshCharacter($characterId);
        });

        return $deletesThread
            ? redirect()->route('board')
            : redirect()->route('thread.view', ['thread' => $thread->id]);
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

    private function resolveCharacterForCreate(Request $request, ForumThread $thread, array $data, int $time): Character
    {
        if ($data['character'] !== 'new') {
            if (! ctype_digit((string) $data['character'])) {
                throw ValidationException::withMessages([
                    'character' => 'Bitte wähle einen Charakter aus.',
                ]);
            }

            return $this->userCharacter((int) $data['character']);
        }

        abort_unless($this->permissionService->allows('createcharacter', $thread, $request->user()), 403);

        $name = trim((string) ($data['newcharname'] ?? ''));

        if ($name === '') {
            throw ValidationException::withMessages([
                'newcharname' => 'Bitte gib einen Namen für den neuen Charakter ein.',
            ]);
        }

        return Character::create([
            'name' => $name,
            'regdate' => $time,
            'user_id' => $request->user()->id,
            'usertext' => '',
        ]);
    }

    private function postUrl(Post $post, int|string|null $page = null): string
    {
        $page ??= $post->pageInThread(self::PAGE_ENTRIES);

        return url('/thread/view/'.$post->thread_id.($page === 1 ? '' : '/'.$page).'#post'.$post->id);
    }
}
