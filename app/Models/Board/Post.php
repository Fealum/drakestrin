<?php

namespace App\Models\Board;

use App\Models\Character;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $table = 'dra_post';

    protected $fillable = [
        'board',
        'thread',
        'user',
        'character',
        'time',
        'message',
        'smilies',
        'signature',
        'ip',
    ];

    public $timestamps = false;

    protected $dateFormat = 'U';

    protected $casts = [
        'board' => 'integer',
        'thread' => 'integer',
        'user' => 'integer',
        'character' => 'integer',
        'time' => 'datetime',
        'smilies' => 'boolean',
        'signature' => 'boolean',
    ];

    public function boardModel(): BelongsTo
    {
        return $this->belongsTo(Board::class, 'board');
    }

    public function threadModel(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'thread');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function characterModel(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'character');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'post');
    }

    public function pageInThread(int $perPage): int
    {
        $postsBefore = self::query()
            ->where('thread', $this->thread)
            ->where(function ($query) {
                $query->where('time', '<', $this->getRawOriginal('time'))
                    ->orWhere(function ($query) {
                        $query->where('time', $this->getRawOriginal('time'))
                            ->where('id', '<=', $this->id);
                    });
            })
            ->count();

        return (int) ceil(max($postsBefore, 1) / $perPage);
    }
}
