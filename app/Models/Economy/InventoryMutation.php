<?php

namespace App\Models\Economy;

use App\Support\InventoryMutationClock;
use App\Support\InventoryMutationKind;
use Illuminate\Database\Eloquent\Model;

class InventoryMutation extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'inventory_id',
        'item_id',
        'kind',
        'clock',
        'effective_at',
        'source_type',
        'source_id',
        'before_state',
        'after_state',
    ];

    protected $casts = [
        'inventory_id' => 'integer',
        'item_id' => 'integer',
        'kind' => InventoryMutationKind::class,
        'clock' => InventoryMutationClock::class,
        'effective_at' => 'integer',
        'source_id' => 'integer',
        'before_state' => 'array',
        'after_state' => 'array',
    ];
}
