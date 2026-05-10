<?php

namespace App\Policies\Board;

use App\Models\Board\Post;
use App\Models\Board\Thread;
use App\Models\User;
use App\Services\PermissionService;

class PostPolicy
{
    public function __construct(private PermissionService $permissions)
    {
    }

    public function create(User $user, Thread $thread): bool
    {
        return $this->permissions->allows('createpost', $thread, $user);
    }

    public function update(User $user, Post $post): bool
    {
        return $this->permissions->allowsOwn('editpost', $post, $post->user, $user);
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->permissions->allowsOwn('deletepost', $post, $post->user, $user);
    }

    public function viewIp(User $user, Post $post): bool
    {
        return $this->permissions->allows('viewip', $post, $user);
    }
}
