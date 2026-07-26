<?php

namespace App\Models\Board;

use App\Models\Economy\Transfer;
use App\Models\Territory\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThreadScene extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'location_id',
        'starts_at_post_id',
        'ends_at_post_id',
        'story_started_at',
        'story_ended_at',
        'ended_at',
        'created_by_user_id',
    ];

    protected $casts = [
        'thread_id' => 'integer',
        'location_id' => 'integer',
        'starts_at_post_id' => 'integer',
        'ends_at_post_id' => 'integer',
        'story_started_at' => 'integer',
        'story_ended_at' => 'integer',
        'ended_at' => 'datetime',
        'created_by_user_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::updating(function (self $scene) {
            $immutable = ['thread_id', 'location_id', 'starts_at_post_id', 'story_started_at', 'created_by_user_id'];

            if ($scene->isDirty($immutable) && $scene->transfers()->exists()) {
                throw new \LogicException('A scene with transfers cannot change its identity or story start.');
            }
        });

        static::deleting(function (self $scene) {
            if ($scene->transfers()->exists()) {
                throw new \LogicException('A scene with transfers cannot be deleted.');
            }
        });
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function startsAtPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'starts_at_post_id');
    }

    public function endsAtPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'ends_at_post_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    public function isActive(): bool
    {
        return $this->ended_at === null;
    }
}
