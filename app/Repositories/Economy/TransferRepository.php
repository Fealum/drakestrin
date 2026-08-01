<?php

namespace App\Repositories\Economy;

use App\Models\Economy\Transfer;
use App\Support\PermissionEntityType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransferRepository
{
    /** @param list<int> $ids */
    public function paginateForParticipants(
        PermissionEntityType $type,
        array $ids,
        int $perPage = 20,
        string $pageName = 'transfers',
    ): LengthAwarePaginator {
        return Transfer::query()
            ->where(function ($query) use ($type, $ids) {
                $query->where(function ($query) use ($type, $ids) {
                    $query->where('sender_type', $type->value)->whereIn('sender_id', $ids);
                })->orWhere(function ($query) use ($type, $ids) {
                    $query->where('recipient_type', $type->value)->whereIn('recipient_id', $ids);
                });
            })
            ->with([
                'items.item', 'actor', 'post.thread', 'recipient', 'reversal',
                'reversalOf', 'scene.location', 'sender',
            ])
            ->orderByDesc('story_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], $pageName)
            ->withQueryString();
    }

    public function paginateForParticipant(
        PermissionEntityType $type,
        int $id,
        int $perPage = 20,
        string $pageName = 'transfers',
    ): LengthAwarePaginator {
        return Transfer::query()
            ->involving($type, $id)
            ->with([
                'items.item',
                'actor',
                'post.thread',
                'recipient',
                'reversal',
                'reversalOf',
                'scene.location',
                'sender',
            ])
            ->orderByDesc('story_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], $pageName)
            ->withQueryString();
    }
}
