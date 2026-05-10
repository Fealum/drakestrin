<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $table = 'dra_inventory';

    public $timestamps = false;

    protected $casts = [
        'item' => 'integer',
        'stack' => 'integer',
        'wear' => 'integer',
        'owner' => 'integer',
        'table__owner' => 'integer',
    ];

    protected $fillable = [
        'item',
        'stack',
        'wear',
        'owner',
        'table__owner',
        'timelastvalue',
        'data',
    ];

    public function itemModel(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item');
    }

    public function makeunitary(): int|string
    {
        return $this->itemModel?->makeunitary($this->stack) ?? $this->stack;
    }

    public function undounitary(int|string|null $stack): int
    {
        return $this->itemModel?->undounitary($stack) ?? (int) $stack;
    }
}
