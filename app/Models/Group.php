<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'priority',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function permissions(): MorphMany
    {
        return $this->morphMany(Permission::class, 'recipient');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
