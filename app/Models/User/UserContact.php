<?php

namespace App\Models\User;

use App\Models\User as Account;
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
        return $this->belongsTo(Account::class);
    }

    public function protocol(): BelongsTo
    {
        return $this->belongsTo(Protocol::class);
    }
}
