<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dra_conversation';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    const CREATED_AT = 'time';
    const UPDATED_AT = null;

    protected $fillable = [
        'view',
        'time',
        'message'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $casts = [
        'time' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'user__sender');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'user__recipient');
    }
}
