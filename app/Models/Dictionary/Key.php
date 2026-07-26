<?php

namespace App\Models\Dictionary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Key extends Model
{
    protected $fillable = [
        'from_word_id',
        'to_word_id',
    ];

    public $timestamps = false;

    protected $casts = [
        'from_word_id' => 'integer',
        'to_word_id' => 'integer',
    ];

    public function fromWord(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }

    public function toWord(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }
}
