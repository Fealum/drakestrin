<?php

namespace App\Models\Board;

use App\Models\Character;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_id',
        'thread_id',
        'user_id',
        'character_id',
        'time',
        'message',
        'smilies',
        'signature',
        'ip',
    ];

    public $timestamps = false;

    protected $dateFormat = 'U';

    protected $casts = [
        'board_id' => 'integer',
        'thread_id' => 'integer',
        'user_id' => 'integer',
        'character_id' => 'integer',
        'time' => 'datetime',
        'smilies' => 'boolean',
        'signature' => 'boolean',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    public function pageInThread(int $perPage): int
    {
        $postsBefore = self::query()
            ->where('thread_id', $this->thread_id)
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
