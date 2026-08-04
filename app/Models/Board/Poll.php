<?php

namespace App\Models\Board;

use App\Support\PollVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    protected $fillable = ['post_element_id', 'question', 'visibility', 'max_choices', 'closes_at'];

    protected $casts = [
        'post_element_id' => 'integer',
        'visibility' => PollVisibility::class,
        'max_choices' => 'integer',
        'closes_at' => 'integer',
    ];

    protected static function booted(): void
    {
        static::updating(function (self $poll) {
            if ($poll->isDirty(['post_element_id', 'question', 'visibility', 'max_choices', 'closes_at'])) {
                throw new \LogicException('Published poll settings are immutable.');
            }
        });
    }

    public function element(): BelongsTo
    {
        return $this->belongsTo(PostElement::class, 'post_element_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class)->orderBy('position');
    }

    public function participations(): HasMany
    {
        return $this->hasMany(PollParticipation::class);
    }

    public function isClosed(): bool
    {
        return $this->closes_at !== null && $this->closes_at <= now()->timestamp;
    }
}
