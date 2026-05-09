<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'dra_post';

    protected $fillable = [
        'board',
        'thread',
        'user',
        'character',
        'time',
        'message',
        'smilies',
        'signature',
        'ip',
    ];

    public $timestamps = false;

    protected $dateFormat = 'U';

    protected $casts = [
        'time' => 'datetime',
    ];
}
