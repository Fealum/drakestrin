<?php

namespace App\Services;

use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread;
use App\Models\Character;
use App\Models\Group;
use App\Models\Page;
use App\Models\Permit;
use App\Models\User;
use App\Support\LegacyTable;
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
        $type = $this->legacyTableFor($object);

        if ($type !== null && isset($this->permissions[$type][$object->id][$action])) {
            return (int) $this->permissions[$type][$object->id][$action];
        }

        $parent = $this->permissionParent($object);

        if ($parent) {
            return $this->resolveObjectPermission($action, $parent);
        }

        return (int) ($this->permits[$action] ?? 0);
    }

    private function legacyTableFor(Model $object): ?int
    {
        return match (true) {
            $object instanceof User => LegacyTable::USER,
            $object instanceof Thread => LegacyTable::THREAD,
            $object instanceof Board => LegacyTable::BOARD,
            $object instanceof Group => LegacyTable::GROUP,
            $object instanceof Page => LegacyTable::ENCYCLOPEDIA,
            $object instanceof Character => LegacyTable::CHARACTER,
            default => null,
        };
    }

    private function permissionParent(Model $object): ?Model
    {
        return match (true) {
            $object instanceof Post => $object->threadModel,
            $object instanceof Thread => $object->boardModel,
            $object instanceof Board => $object->board ? $object->parentBoard : null,
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
