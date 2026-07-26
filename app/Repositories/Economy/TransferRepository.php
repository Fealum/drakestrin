<?php

namespace App\Repositories\Economy;

use App\Models\Economy\Transfer;
use App\Support\PermissionEntityType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransferRepository
{
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
