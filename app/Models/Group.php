<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Group extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dra_group';

    protected $fillable = [
        'name',
        'group__parent',
        'priority',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group__parent');
    }

    public function permissions(): MorphMany
    {
        return $this->morphMany(Permission::class, 'recipient_legacy', 'table__recipient', 'recipient');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'dra_group2user', 'group', 'user');
    }
}
