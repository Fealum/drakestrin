<?php

namespace App\Models\Economy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'transfer_id',
        'item_id',
        'inventory_id',
        'inventory_state',
        'stack',
    ];

    protected $casts = [
        'transfer_id' => 'integer',
        'item_id' => 'integer',
        'inventory_id' => 'integer',
        'inventory_state' => 'array',
        'stack' => 'integer',
    ];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
