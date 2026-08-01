<?php

namespace App\Models\Economy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyWorker extends Model
{
    use HasFactory;

    public const SALARY_PERIOD_SECONDS = 2592000;

    public const STRIKE_AFTER_PERIODS = 3;

    protected $dateFormat = 'U';

    protected $fillable = [
        'name',
        'type',
        'company_id',
        'company_site_id',
        'hired',
        'paid',
    ];

    public $timestamps = false;

    protected $casts = [
        'type' => 'integer',
        'company_id' => 'integer',
        'company_site_id' => 'integer',
        'hired' => 'datetime',
        'paid' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(CompanySite::class, 'company_site_id');
    }

    public function activeLabours(): HasMany
    {
        return $this->hasMany(LabourActive::class)
            ->whereNull('ended_at')
            ->with('labour.components.item')
            ->orderBy('since');
    }

    public function productionRuns(): HasMany
    {
        return $this->hasMany(ProductionRun::class)
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->orderByDesc('id');
    }

    public function isOnStrikeAt(?int $timestamp = null): bool
    {
        $timestamp ??= now()->timestamp;

        return ! $this->paid
            || $this->paid->timestamp <= $timestamp - (self::STRIKE_AFTER_PERIODS * self::SALARY_PERIOD_SECONDS);
    }

    public function strikeStartedAt(): ?int
    {
        if (! $this->paid) {
            return null;
        }

        return $this->paid->timestamp + (self::STRIKE_AFTER_PERIODS * self::SALARY_PERIOD_SECONDS);
    }

    public function salaryStatus(?int $timestamp = null): ?string
    {
        $timestamp ??= now()->timestamp;

        if (! $this->paid) {
            return 'im Streik';
        }

        $months = (int) floor(($timestamp - $this->paid->timestamp) / self::SALARY_PERIOD_SECONDS);

        return match (true) {
            $months > 4 => 'im Streik ('.$months.' Monate ohne Gehalt)',
            $this->isOnStrikeAt($timestamp) => 'im Streik',
            $months >= 2 => 'überfällig',
            $months >= 1 => 'fällig',
            default => null,
        };
    }
}
