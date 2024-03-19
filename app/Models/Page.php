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

    protected $fillable = [
        'sort',
        'name',
        'title',
        'slug',
        'page_id',
        'text',
        'user_id',
        'created_at',
        'activated',
    ];

    protected $dateFormat = 'U';

    const UPDATED_AT = null;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'page_id')->orderBy('sort')->orderBy('name');
    }

    public function textFormatted(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => TextFormatter::format($attributes['text']),
        );
    }
}
