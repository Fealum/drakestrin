<?php

namespace App\Models\Territory;

use App\Models\Character;
use App\Models\Concerns\HasSpatialGeometry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Territory extends Model
{
    use HasSpatialGeometry;

    protected $table = 'dra_territory';

    protected $fillable = [
        'name',
        'type',
        'territory',
        'character',
        'area',
        'population',
        'geldstand',
        'beliebtheit',
        'settlement',
    ];

    public $timestamps = false;

    protected $casts = [
        'territory' => 'integer',
        'character' => 'integer',
        'area' => 'integer',
        'population' => 'integer',
        'geldstand' => 'integer',
        'beliebtheit' => 'integer',
        'settlement' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'territory');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'territory')
            ->orderByRaw('LOWER(name)');
    }

    public function ruler(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'character');
    }

    public function capital(): BelongsTo
    {
        return $this->belongsTo(Settlement::class, 'settlement');
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
