<?php

namespace App\Policies\Board;

use App\Models\Board\Thread;
use App\Models\User;
use App\Services\PermissionService;

class ThreadPolicy
{
    public function __construct(private PermissionService $permissions)
    {
    }

    public function view(?User $user, Thread $thread): bool
    {
        return $this->permissions->allows('show', $thread, $user);
    }

    public function create(User $user): bool
    {
        return $this->permissions->allows('createthread', user: $user);
    }

    public function update(User $user, Thread $thread): bool
    {
        return $this->permissions->allowsOwn('editthread', $thread, $thread->firstPost?->user, $user);
    }

    public function delete(User $user, Thread $thread): bool
    {
        return $this->permissions->allowsOwn('deletethread', $thread, $thread->firstPost?->user, $user);
    }

    public function markAsImportant(User $user, Thread $thread): bool
    {
        if (! $this->permissions->hasPermit('markasimportant')) {
            return true;
        }

        return $this->permissions->allows('markasimportant', $thread, $user);
    }
}
