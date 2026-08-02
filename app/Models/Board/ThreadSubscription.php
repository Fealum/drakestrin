<?php

namespace App\Models\Board;

use App\Models\User;
use App\Support\ThreadEmailFrequency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreadSubscription extends Model
{
    protected $fillable = ['user_id', 'thread_id', 'email_frequency', 'last_emailed_post_id'];

    protected $casts = [
        'user_id' => 'integer',
        'thread_id' => 'integer',
        'last_emailed_post_id' => 'integer',
        'email_frequency' => ThreadEmailFrequency::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dateFormat = 'U';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }
}
