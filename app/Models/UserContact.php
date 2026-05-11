<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserContact extends Model
{
    protected $table = 'dra_user_contact';

    public $timestamps = false;

    protected $fillable = [
        'user',
        'protocol',
        'contact',
    ];

    protected $casts = [
        'user' => 'integer',
        'protocol' => 'integer',
    ];

    public function userModel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function protocolModel(): BelongsTo
    {
        return $this->belongsTo(Protocol::class, 'protocol');
    }
}
