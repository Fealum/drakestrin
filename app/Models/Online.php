<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class Online extends Model
{
    use HasFactory;

    protected $fillable = [
        'time',
        'ip',
        'user_id',
        'browser',
        'controller',
        'action',
        'location',
        'route',
        'locateable_id',
        'locateable_type',
    ];

    public $timestamps = false;

    protected $dateFormat = 'U';

    protected $casts = [
        'time' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function locateable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function pruneStale(int $timeoutMinutes): void
    {
        foreach (static::with('user')->where('time', '<', now()->subMinutes($timeoutMinutes)->getTimestamp())->get() as $oldOnline) {
            $user = $oldOnline->user;

            if ($user) {
                $user->lastvisit = $oldOnline->time;
                $user->save();
            }

            $oldOnline->delete();
        }

        static::whereDoesntHave('user')->delete();
    }

    public static function currentEntries(): Collection
    {
        return static::with(['locateable', 'user'])
            ->whereHas('user')
            ->orderByDesc('time')
            ->get();
    }
}
