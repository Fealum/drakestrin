<?php

namespace App\Models\Board;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Permission;

class Board extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'password',
        'description',
        'thread_count',
        'post_count',
        'last_post_at',
        'last_post_id',
        'sort',
        'cat',
        'invisible',
        'style_set',
        'countposts',
        'board_extragroups',
        'hp',
    ];

    public $timestamps = false;

    protected $casts = [
        'parent_id' => 'integer',
        'thread_count' => 'integer',
        'post_count' => 'integer',
        'last_post_at' => 'datetime',
        'last_post_id' => 'integer',
        'sort' => 'integer',
        'cat' => 'boolean',
        'invisible' => 'boolean',
        'style_set' => 'integer',
    ];

    protected $dateFormat = 'U';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort')
            ->orderByRaw('LOWER(name)');
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class)
            ->orderByDesc('important')
            ->orderByDesc('last_post_at');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function lastPost(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function permissionRules(): MorphMany
    {
        return $this->morphMany(Permission::class, 'subject');
    }
}
