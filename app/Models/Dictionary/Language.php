<?php

namespace App\Models\Dictionary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public $timestamps = false;

    public function words(): HasMany
    {
        return $this->hasMany(Word::class);
    }
}
