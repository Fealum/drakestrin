<?php

namespace App\Policies\Board;

use App\Models\Board\Board;
use App\Models\User;
use App\Services\PermissionService;

class BoardPolicy
{
    public function __construct(private PermissionService $permissions)
    {
    }

    public function view(?User $user, Board $board): bool
    {
        return $this->permissions->allows('show', $board, $user);
    }

    public function createThread(User $user, Board $board): bool
    {
        return ! $board->cat && $this->permissions->allows('createthread', $board, $user);
    }

    public function update(User $user, Board $board): bool
    {
        return $this->permissions->allows('editboard', $board, $user);
    }

    public function delete(User $user, Board $board): bool
    {
        return $this->permissions->allows('deleteboard', $board, $user);
    }
}
