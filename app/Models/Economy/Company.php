<?php

namespace App\Models\Economy;

use App\Models\Board\Thread;
use App\Models\Territory\Territory;
use App\Models\User\Character;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'character_id',
        'created_by_user_id',
        'description',
        'text',
        'territory_id',
        'thread_id',
        'url',
        'volksgeld',
    ];

    protected $casts = [
        'type' => 'integer',
        'character_id' => 'integer',
        'created_by_user_id' => 'integer',
        'territory_id' => 'integer',
        'thread_id' => 'integer',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function territory(): BelongsTo
    {
        return $this->belongsTo(Territory::class);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function workers(): HasMany
    {
        return $this->hasMany(CompanyWorker::class)
            ->with('activeLabours.labour')
            ->orderByDesc('type')
            ->orderByRaw('LOWER(name)');
    }

    public function sites(): HasMany
    {
        return $this->hasMany(CompanySite::class)
            ->with('location')
            ->orderByDesc('is_headquarters')
            ->orderByDesc('is_storefront')
            ->orderBy('id');
    }

    public function headquarters(): HasOne
    {
        return $this->hasOne(CompanySite::class)->where('is_headquarters', true);
    }

    public function representatives(): HasMany
    {
        return $this->hasMany(CompanyRepresentative::class)
            ->with('character.user')
            ->orderBy('role')
            ->orderBy('id');
    }

    public function isRepresentedBy(Character $character): bool
    {
        return (int) $this->character_id === (int) $character->id
            || $this->representatives()->where('character_id', $character->id)->exists();
    }

    public function inventory(): MorphMany
    {
        return $this->morphMany(Inventory::class, 'owner')
            ->with('item')
            ->orderBy('wear')
            ->orderBy('id');
    }

    public function productionRuns(): HasMany
    {
        return $this->hasMany(ProductionRun::class)
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->orderByDesc('id');
    }
}
