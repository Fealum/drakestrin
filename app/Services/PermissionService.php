<?php

namespace App\Services;

use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread;
use App\Models\Access\Permit;
use App\Models\User;
use App\Models\Territory\Location;
use App\Support\PermissionEntityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PermissionService
{
    private ?array $permissions = null;
    private ?array $permits = null;
    private int|string|null $loadedUserId = null;

    private function instantiate(?User $user = null): void
    {
        $userId = $user?->id ?? auth()->id();
        $permissionCacheKey = 'user_permissions:' . ($userId ?? 'global');
        $permitCacheKey = 'user_permits:' . ($userId ?? 'global');

        $this->permissions = Cache::get($permissionCacheKey, $this->defaultPermissions());
        $this->permits = Cache::get($permitCacheKey, $this->defaultPermits());
        $this->loadedUserId = $userId ?? 'global';
    }

    public function check(string $action, ?Model $object = null, ?User $user = null): int
    {
        $action = strtolower($action);

        $userId = $user?->id ?? auth()->id() ?? 'global';

        if ($this->permissions === null || $this->permits === null || $this->loadedUserId !== $userId) {
            $this->instantiate($user);
        }

        if ($object === null) {
            return (int) ($this->permits[$action] ?? 0);
        }

        return $this->resolveObjectPermission($action, $object);
    }

    public function allows(string $action, ?Model $object = null, ?User $user = null): bool
    {
        return $this->check($action, $object, $user) > 0;
    }

    public function allowsOwn(string $action, Model $object, ?int $ownerId, User $user): bool
    {
        $value = $this->check($action, $object, $user);

        return $value === 2 || ($value === 1 && $ownerId === $user->id);
    }

    public function hasPermit(string $action): bool
    {
        if ($this->permits === null) {
            $this->instantiate();
        }

        return array_key_exists(strtolower($action), $this->permits);
    }

    private function resolveObjectPermission(string $action, Model $object): int
    {
        $type = $this->entityTypeFor($object);

        if ($type !== null && isset($this->permissions[$type->value][$object->id][$action])) {
            return (int) $this->permissions[$type->value][$object->id][$action];
        }

        $parent = $this->permissionParent($object);

        if ($parent) {
            return $this->resolveObjectPermission($action, $parent);
        }

        return (int) ($this->permits[$action] ?? 0);
    }

    private function entityTypeFor(Model $object): ?PermissionEntityType
    {
        return PermissionEntityType::fromModel($object);
    }

    private function permissionParent(Model $object): ?Model
    {
        return match (true) {
            $object instanceof Post => $object->thread,
            $object instanceof Thread => $object->board,
            $object instanceof Board => $object->parent_id ? $object->parent : null,
            $object instanceof Location => $object->parent,
            default => null,
        };
    }

    private function defaultPermits(): array
    {
        return Permit::query()
            ->pluck('standard', 'name')
            ->map(fn ($value) => (int) $value)
            ->all();
    }

    private function defaultPermissions(): array
    {
        return [];
    }
}
