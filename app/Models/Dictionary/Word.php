<?php

namespace App\Models\Dictionary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Word extends Model
{
    protected $fillable = [
        'language_id',
        'word_type_id',
        'word',
        'val',
    ];

    public $timestamps = false;

    protected $casts = [
        'language_id' => 'integer',
        'word_type_id' => 'integer',
        'val' => 'integer',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function wordType(): BelongsTo
    {
        return $this->belongsTo(WordType::class);
    }

    public function translationKeysFrom(): HasMany
    {
        return $this->hasMany(Key::class, 'from_word_id');
    }

    public function translationKeysTo(): HasMany
    {
        return $this->hasMany(Key::class, 'to_word_id');
    }

    public function translations(): BelongsToMany
    {
        return $this->belongsToMany(
            Word::class,
            'keys',
            'from_word_id',
            'to_word_id'
        )->withPivot('id');
    }

    public function reverseTranslations(): BelongsToMany
    {
        return $this->belongsToMany(
            Word::class,
            'keys',
            'to_word_id',
            'from_word_id'
        )->withPivot('id');
    }
}
