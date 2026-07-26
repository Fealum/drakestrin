<?php

namespace App\Models\User;

use App\Models\User as Account;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    const UPDATED_AT = null;

    protected $fillable = [
        'view',
        'message'
    ];

    public function sender()
    {
        return $this->belongsTo(Account::class, 'sender_user_id');
    }

    public function recipient()
    {
        return $this->belongsTo(Account::class, 'recipient_user_id');
    }
}
