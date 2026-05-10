<?php

namespace App\Models\Board;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Permission;

class Board extends Model
{
    protected $table = 'dra_board';

    protected $fillable = [
        'board',
        'name',
        'password',
        'description',
        'thread__total',
        'post__total',
        'post__last_time',
        'post__last',
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
        'board' => 'integer',
        'thread__total' => 'integer',
        'post__total' => 'integer',
        'post__last_time' => 'datetime',
        'post__last' => 'integer',
        'sort' => 'integer',
        'cat' => 'boolean',
        'invisible' => 'boolean',
        'style_set' => 'integer',
    ];

    protected $dateFormat = 'U';

    public function parentBoard(): BelongsTo
    {
        return $this->belongsTo(self::class, 'board');
    }

    public function childBoards(): HasMany
    {
        return $this->hasMany(self::class, 'board')
            ->orderBy('sort')
            ->orderByRaw('LOWER(name)');
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class, 'board')
            ->orderByDesc('important')
            ->orderByDesc('post__last_time');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'board');
    }

    public function lastPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post__last');
    }

    public function permissionsAsSubject(): MorphMany
    {
        return $this->morphMany(Permission::class, 'subject_legacy', 'table__subject', 'subject');
    }
}
