<?php

namespace App\Models;

use App\Models\Territory\Territory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
