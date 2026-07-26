<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'standard',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
