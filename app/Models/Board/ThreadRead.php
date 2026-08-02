<?php

namespace App\Models\Board;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreadRead extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'thread_id', 'last_read_post_id', 'read_at'];

    protected $casts = [
        'user_id' => 'integer',
        'thread_id' => 'integer',
        'last_read_post_id' => 'integer',
        'read_at' => 'datetime',
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
