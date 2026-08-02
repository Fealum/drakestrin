<?php

namespace App\Models\User;

use App\Models\User;
use App\Support\ThreadEmailFrequency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'auto_subscribe', 'default_email_frequency', 'read_tracking_started_at', 'read_tracking_started_post_id',
        'last_daily_digest_at', 'last_weekly_digest_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'auto_subscribe' => 'boolean',
        'default_email_frequency' => ThreadEmailFrequency::class,
        'read_tracking_started_at' => 'datetime',
        'read_tracking_started_post_id' => 'integer',
        'last_daily_digest_at' => 'datetime',
        'last_weekly_digest_at' => 'datetime',
    ];

    protected $dateFormat = 'U';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
