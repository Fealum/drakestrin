<?php

namespace App\Models\Board;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PollParticipation extends Model
{
    protected $fillable = ['poll_id', 'user_id'];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function choices(): BelongsToMany
    {
        return $this->belongsToMany(PollOption::class, 'poll_choices');
    }
}
