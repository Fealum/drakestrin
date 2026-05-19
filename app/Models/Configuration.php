<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'recipient_type',
        'recipient_id',
        'subject_type',
        'subject_id',
        'setting',
        'value',
    ];

    public $timestamps = false;

    protected $casts = [
        'recipient_type' => 'integer',
        'recipient_id' => 'integer',
        'subject_type' => 'integer',
        'subject_id' => 'integer',
        'setting' => 'integer',
        'value' => 'integer',
    ];
}
