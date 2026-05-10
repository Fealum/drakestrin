<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferItem extends Model
{
    protected $table = 'dra_transferitem';

    public $timestamps = false;

    protected $fillable = [
        'transfer',
        'item',
        'stack',
    ];

    protected $casts = [
        'transfer' => 'integer',
        'item' => 'integer',
        'stack' => 'integer',
    ];

    public function transferModel(): BelongsTo
    {
        return $this->belongsTo(Transfer::class, 'transfer');
    }

    public function itemModel(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item');
    }
}
