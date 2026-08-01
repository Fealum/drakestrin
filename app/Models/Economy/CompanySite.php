<?php

namespace App\Models\Economy;

use App\Models\Territory\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CompanySite extends Model
{
    protected $fillable = [
        'company_id',
        'location_id',
        'name',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'location_id' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function inventory(): MorphMany
    {
        return $this->morphMany(Inventory::class, 'owner')
            ->with('item')
            ->orderBy('wear')
            ->orderBy('id');
    }

    public function workers(): HasMany
    {
        return $this->hasMany(CompanyWorker::class)
            ->with('activeLabours.labour')
            ->orderByDesc('type')
            ->orderByRaw('LOWER(name)');
    }

    public function productionRuns(): HasMany
    {
        return $this->hasMany(ProductionRun::class)
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->orderByDesc('id');
    }

    public function representatives(): HasMany
    {
        return $this->hasMany(CompanyRepresentative::class)
            ->with('character.user')
            ->orderBy('role')
            ->orderBy('id');
    }

    public function isHeadquarters(): bool
    {
        return (int) $this->company?->headquarters_site_id === (int) $this->id;
    }
}
