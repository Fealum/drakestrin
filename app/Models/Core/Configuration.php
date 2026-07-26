<?php

namespace App\Models\Core;

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
        'recipient_id' => 'integer',
        'subject_id' => 'integer',
        'setting' => 'integer',
        'value' => 'integer',
    ];
}
