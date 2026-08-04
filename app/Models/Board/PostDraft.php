<?php

namespace App\Models\Board;

use App\Models\User;
use App\Models\User\Character;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostDraft extends Model
{
    protected $fillable = ['user_id', 'thread_id', 'board_id', 'title', 'character_id', 'payload', 'version'];

    protected $casts = [
        'user_id' => 'integer',
        'thread_id' => 'integer',
        'board_id' => 'integer',
        'character_id' => 'integer',
        'payload' => 'array',
        'version' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}
