<?php

namespace App\Models;

use App\Helpers\TextFormatter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dra_encyclopedia';

    protected $fillable = [
        'sort',
        'name',
        'title',
        'encyclopedia',
        'text',
        'user',
        'time',
        'activated',
    ];

    protected $dateFormat = 'U';

    const CREATED_AT = 'time';
    const UPDATED_AT = null;

    protected $casts = [
        'time' => 'datetime',
    ];

    public function userLegacy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'encyclopedia');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'encyclopedia')->orderBy('sort')->orderBy('name');
    }

    public function textFormatted(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => TextFormatter::format($attributes['text']),
        );
    }
}
