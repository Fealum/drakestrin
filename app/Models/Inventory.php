<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Inventory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'item_id' => 'integer',
        'stack' => 'integer',
        'wear' => 'integer',
        'owner_id' => 'integer',
        'owner_type' => 'integer',
    ];

    protected $fillable = [
        'item_id',
        'stack',
        'wear',
        'owner_id',
        'owner_type',
        'timelastvalue',
        'data',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function makeunitary(): int|string
    {
        return $this->item?->makeunitary($this->stack) ?? $this->stack;
    }

    public function undounitary(int|string|null $stack): int
    {
        return $this->item?->undounitary($stack) ?? (int) $stack;
    }
}
