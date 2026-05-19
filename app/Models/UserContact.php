<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserContact extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'protocol_id',
        'contact',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'protocol_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function protocol(): BelongsTo
    {
        return $this->belongsTo(Protocol::class);
    }
}
