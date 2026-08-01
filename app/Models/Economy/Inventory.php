<?php

namespace App\Models\Economy;

use App\Support\InventoryStockState;
use App\Support\PermissionEntityType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

class Inventory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'item_id' => 'integer',
        'stack' => 'integer',
        'wear' => 'integer',
        'owner_id' => 'integer',
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

    protected static function booted(): void
    {
        static::saving(function (Inventory $inventory): void {
            $stackable = Item::query()->whereKey($inventory->item_id)->value('stackable');

            if ($stackable === null) {
                throw new LogicException('An inventory row must reference an existing item.');
            }

            if ((bool) $stackable && (int) $inventory->stack <= 0) {
                throw new LogicException('A stackable inventory row must have a positive quantity.');
            }

            if (! (bool) $stackable && (int) $inventory->stack !== 0) {
                throw new LogicException('A non-stackable inventory row must represent exactly one item instance.');
            }
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(InventoryMutation::class)->orderBy('id');
    }

    public function scopeOwnedBy(Builder $query, PermissionEntityType $ownerType, int $ownerId): Builder
    {
        return $query
            ->where('owner_type', $ownerType->value)
            ->where('owner_id', $ownerId);
    }

    public function makeunitary(): int|string
    {
        return $this->item?->makeunitary($this->stack) ?? $this->stack;
    }

    public function undounitary(int|string|null $stack): int
    {
        return $this->item?->undounitary($stack) ?? (int) $stack;
    }

    public function stockState(): ?InventoryStockState
    {
        return InventoryStockState::tryFrom((int) $this->wear);
    }

    public function isForSale(): bool
    {
        return (int) $this->wear >= 0;
    }
}
