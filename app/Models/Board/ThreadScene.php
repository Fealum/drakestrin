<?php

namespace App\Models\Board;

use App\Models\Territory\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function isActive(): bool
    {
        return $this->ended_at === null;
    }
}
