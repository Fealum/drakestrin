<?php

namespace App\Models;

use App\Models\Territory\Territory;
use App\Models\Board\Thread;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Company extends Model
{
    public $timestamps = false;

    protected $casts = [
        'type' => 'integer',
        'character_id' => 'integer',
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

    public function inventory(): MorphMany
    {
        return $this->morphMany(Inventory::class, 'owner')
            ->with('item')
            ->orderBy('wear')
            ->orderBy('id');
    }
}
