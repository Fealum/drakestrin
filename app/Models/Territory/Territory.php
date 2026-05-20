<?php

namespace App\Models\Territory;

use App\Models\Character;
use App\Models\Concerns\HasSpatialGeometry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Territory extends Model
{
    use HasFactory, HasSpatialGeometry;

    protected $fillable = [
        'name',
        'type',
        'parent_id',
        'character_id',
        'area',
        'population',
        'geldstand',
        'beliebtheit',
        'capital_id',
    ];

    public $timestamps = false;

    protected $casts = [
        'parent_id' => 'integer',
        'character_id' => 'integer',
        'area' => 'integer',
        'population' => 'integer',
        'geldstand' => 'integer',
        'beliebtheit' => 'integer',
        'capital_id' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderByRaw('LOWER(name)');
    }

    public function ruler(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'character_id');
    }

    public function capital(): BelongsTo
    {
        return $this->belongsTo(Settlement::class);
    }

    public function typeLabel(): ?string
    {
        return match ($this->type) {
            '1' => 'Atlas',
            '2' => 'Provinz',
            '3a' => 'Herzogtum',
            '3b' => 'Grafschaft',
            '3c' => 'Baronie',
            '4' => 'Burgschaft',
            '5' => 'Fauthei',
            default => null,
        };
    }

    public function displayName(): string
    {
        return trim(collect([$this->typeLabel(), $this->name])->filter()->implode(' '));
    }

    public function rulerTitle(): ?string
    {
        $isFemale = (int) $this->ruler?->gender === 2;

        return match ($this->type) {
            '1' => 'Kaiser',
            '2' => $isFemale ? 'Königin' : 'König',
            '3a' => $isFemale ? 'Herzogin' : 'Herzog',
            '3b' => $isFemale ? 'Gräfin' : 'Graf',
            '3c' => $isFemale ? 'Baronin' : 'Baron',
            '4' => 'Statthalter',
            '5' => $isFemale ? 'Fauthin' : 'Fauth',
            default => null,
        };
    }

    public function populationDensity(): ?float
    {
        if (! $this->area) {
            return null;
        }

        return $this->population / ($this->area / 1000000);
    }
}
