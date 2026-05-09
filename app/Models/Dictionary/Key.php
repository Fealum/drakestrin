<?php

namespace App\Models\Dictionary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Key extends Model
{
    protected $table = 'dra_dictionarykey';

    protected $fillable = [
        'dictionary__from',
        'dictionary__to',
    ];

    public $timestamps = false;

    protected $casts = [
        'dictionary__from' => 'integer',
        'dictionary__to' => 'integer',
    ];

    public function fromWord(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'dictionary__from');
    }

    public function toWord(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'dictionary__to');
    }
}
