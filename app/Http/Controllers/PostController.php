<?php

namespace App\Http\Controllers;

use App\Http\Requests\Board\DestroyPostRequest;
use App\Http\Requests\Board\StorePostRequest;
use App\Http\Requests\Board\UpdatePostRequest;
use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Services\Board\PostWriter;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    private const PAGE_ENTRIES = 20;

    public function __construct(PermissionService $permissionService, private PostWriter $posts)
    {
        parent::__construct($permissionService);
    }

    public function view(Post $post): RedirectResponse
    {
        return redirect($this->postUrl($post));
    }

    public function create(StorePostRequest $request, ForumThread $thread): RedirectResponse
    {
        $post = $this->posts->create($thread, $request->user(), $request->data(), $request->ip());

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

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->posts->update($post, $request->user(), $request->data());

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

    public function destroy(DestroyPostRequest $request, Post $post): RedirectResponse
    {
        $thread = $post->thread;
        $deletesThread = $this->posts->delete($post);

        return $deletesThread
            ? redirect()->route('board')
            : redirect()->route('thread.view', ['thread' => $thread->id]);
    }

    private function postUrl(Post $post, int|string|null $page = null): string
    {
        $page ??= $post->pageInThread(self::PAGE_ENTRIES);

        return url('/thread/view/'.$post->thread_id.($page === 1 ? '' : '/'.$page).'#post'.$post->id);
    }
}
