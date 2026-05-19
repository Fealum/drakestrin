<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'transfer_id',
        'item_id',
        'stack',
    ];

    protected $casts = [
        'transfer_id' => 'integer',
        'item_id' => 'integer',
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
