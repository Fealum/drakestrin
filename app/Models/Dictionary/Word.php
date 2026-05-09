<?php

namespace App\Models\Dictionary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Word extends Model
{
    protected $table = 'dra_dictionary';

    protected $fillable = [
        'language',
        'wordtype',
        'word',
        'val',
    ];

    public $timestamps = false;

    protected $casts = [
        'language' => 'integer',
        'wordtype' => 'integer',
        'val' => 'integer',
    ];

    public function languageModel(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language');
    }

    public function wordType(): BelongsTo
    {
        return $this->belongsTo(WordType::class, 'wordtype');
    }

    public function translationKeysFrom(): HasMany
    {
        return $this->hasMany(Key::class, 'dictionary__from');
    }

    public function translationKeysTo(): HasMany
    {
        return $this->hasMany(Key::class, 'dictionary__to');
    }

    public function translations(): BelongsToMany
    {
        return $this->belongsToMany(
            Word::class,
            'dra_dictionarykey',
            'dictionary__from',
            'dictionary__to'
        )->withPivot('id');
    }

    public function reverseTranslations(): BelongsToMany
    {
        return $this->belongsToMany(
            Word::class,
            'dra_dictionarykey',
            'dictionary__to',
            'dictionary__from'
        )->withPivot('id');
    }
}
