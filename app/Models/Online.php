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

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dra_online';

    protected $fillable = [
        'time',
        'ip',
        'user',
        'browser',
        'controller',
        'action',
        'table__location',
        'location',
        'route',
        'locateable_id',
        'locateable_type',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    protected $casts = [
        'time' => 'datetime',
    ];

    public function user_legacy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function locateable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function pruneStale(int $timeoutMinutes): void
    {
        foreach (static::with('user_legacy')->where('time', '<', now()->subMinutes($timeoutMinutes)->getTimestamp())->get() as $oldOnline) {
            $user = $oldOnline->user_legacy;

            if ($user) {
                $user->lastvisit = $oldOnline->time;
                $user->save();
            }

            $oldOnline->delete();
        }

        static::whereDoesntHave('user_legacy')->delete();
    }

    public static function currentEntries(): Collection
    {
        return static::with(['locateable', 'user_legacy'])
            ->whereHas('user_legacy')
            ->orderByDesc('time')
            ->get();
    }
}
