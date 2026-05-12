<?php

namespace App\Models;

use App\Models\Territory\Territory;
use App\Models\Board\Thread;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $table = 'dra_company';

    public $timestamps = false;

    protected $casts = [
        'type' => 'integer',
        'character' => 'integer',
        'territory' => 'integer',
        'thread' => 'integer',
    ];

    public function characterModel(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'character');
    }

    public function territoryModel(): BelongsTo
    {
        return $this->belongsTo(Territory::class, 'territory');
    }

    public function threadModel(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'thread');
    }

    public function workers(): HasMany
    {
        return $this->hasMany(CompanyWorker::class, 'company')
            ->with('activeLabours.labourModel')
            ->orderByDesc('type')
            ->orderByRaw('LOWER(name)');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class, 'owner')
            ->where('table__owner', 2)
            ->with('itemModel')
            ->orderBy('wear')
            ->orderBy('id');
    }
}
