<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidEmail extends Model
{
    protected $fillable = [
        'email',
        'valid_until'
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
        'valid_until' => 'datetime',
    ];
}
