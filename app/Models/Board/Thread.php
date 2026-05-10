<?php

namespace App\Models\Board;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    protected $table = 'dra_thread';

    protected $fillable = [
        'board',
        'post__first_time',
        'name',
        'post__total',
        'post__first',
        'post__last',
        'post__last_time',
        'views',
        'flags',
        'topicicon',
        'rate_points',
        'rated',
        'putoffid',
        'important',
        'pquestion',
        'ptimeout',
        'rpg',
        'shopthread',
        'altercat',
    ];

    public $timestamps = false;

    protected $casts = [
        'board' => 'integer',
        'post__first_time' => 'datetime',
        'post__total' => 'integer',
        'post__first' => 'integer',
        'post__last' => 'integer',
        'post__last_time' => 'datetime',
        'views' => 'integer',
        'flags' => 'boolean',
        'rate_points' => 'integer',
        'rated' => 'integer',
        'putoffid' => 'integer',
        'important' => 'boolean',
        'ptimeout' => 'datetime',
        'shopthread' => 'integer',
        'altercat' => 'integer',
    ];

    protected $dateFormat = 'U';

    public function boardModel(): BelongsTo
    {
        return $this->belongsTo(Board::class, 'board');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'thread')
            ->orderBy('time')
            ->orderBy('id');
    }

    public function firstPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post__first');
    }

    public function lastPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post__last');
    }
}
