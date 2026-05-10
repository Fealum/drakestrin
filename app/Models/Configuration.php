<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $table = 'dra_configuration';

    protected $fillable = [
        'table__recipient',
        'recipient',
        'table__subject',
        'subject',
        'setting',
        'value',
    ];

    public $timestamps = false;

    protected $casts = [
        'table__recipient' => 'integer',
        'recipient' => 'integer',
        'table__subject' => 'integer',
        'subject' => 'integer',
        'setting' => 'integer',
        'value' => 'integer',
    ];
}
