<?php

namespace App\Models\Board;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PollOption extends Model
{
    public $timestamps = false;

    protected $fillable = ['poll_id', 'position', 'label', 'unattributed_votes'];

    protected $casts = ['poll_id' => 'integer', 'position' => 'integer', 'unattributed_votes' => 'integer'];

    protected static function booted(): void
    {
        static::updating(function (self $option) {
            if ($option->isDirty(['poll_id', 'position', 'label'])) {
                throw new \LogicException('Published poll options are immutable.');
            }
        });
    }

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function participations(): BelongsToMany
    {
        return $this->belongsToMany(PollParticipation::class, 'poll_choices');
    }

    public function voteCount(): int
    {
        return $this->unattributed_votes + ($this->relationLoaded('participations') ? $this->participations->count() : $this->participations()->count());
    }
}
