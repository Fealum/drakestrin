<?php

namespace App\Models\Board;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_id',
        'first_post_at',
        'name',
        'post_count',
        'first_post_id',
        'last_post_id',
        'last_post_at',
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
        'board_id' => 'integer',
        'first_post_at' => 'datetime',
        'post_count' => 'integer',
        'first_post_id' => 'integer',
        'last_post_id' => 'integer',
        'last_post_at' => 'datetime',
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

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)
            ->orderBy('time')
            ->orderBy('id');
    }

    public function firstPost(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function lastPost(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
