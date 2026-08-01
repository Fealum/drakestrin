<?php

namespace App\Models\Economy;

use App\Models\Board\Thread;
use App\Models\Territory\Territory;
use App\Models\User\Character;
use App\Support\CompanyRepresentativeRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'created_by_user_id',
        'headquarters_site_id',
        'description',
        'territory_id',
        'thread_id',
        'volksgeld',
    ];

    protected $casts = [
        'type' => 'integer',
        'created_by_user_id' => 'integer',
        'headquarters_site_id' => 'integer',
        'territory_id' => 'integer',
        'thread_id' => 'integer',
    ];

    public function territory(): BelongsTo
    {
        return $this->belongsTo(Territory::class);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function workers(): HasMany
    {
        return $this->hasMany(CompanyWorker::class)
            ->with('activeLabours.labour')
            ->orderByDesc('type')
            ->orderByRaw('LOWER(name)');
    }

    public function sites(): HasMany
    {
        return $this->hasMany(CompanySite::class)
            ->with('location')
            ->orderBy('id');
    }

    public function headquarters(): BelongsTo
    {
        return $this->belongsTo(CompanySite::class, 'headquarters_site_id');
    }

    public function owners(): HasMany
    {
        return $this->hasMany(CompanyOwner::class)
            ->with('character.user')
            ->orderBy('id');
    }

    public function representatives(): HasMany
    {
        return $this->hasMany(CompanyRepresentative::class)
            ->with('character.user')
            ->orderBy('role')
            ->orderBy('id');
    }

    public function isRepresentedBy(Character $character): bool
    {
        return $this->owners()->where('character_id', $character->id)->exists()
            || $this->representatives()->where('character_id', $character->id)->exists();
    }

    public function isOwnedByUserId(int $userId): bool
    {
        return $this->owners()
            ->whereHas('character', fn ($query) => $query->where('user_id', $userId))
            ->exists();
    }

    public function isManagedByUserId(int $userId): bool
    {
        return $this->isOwnedByUserId($userId) || $this->representatives()
            ->where('role', CompanyRepresentativeRole::MANAGER->value)
            ->whereHas('character', fn ($query) => $query->where('user_id', $userId))
            ->exists();
    }

    public function productionRuns(): HasMany
    {
        return $this->hasMany(ProductionRun::class)
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->orderByDesc('id');
    }

    public function canChangeSector(): bool
    {
        return ! $this->workers()->exists()
            && ! ProductionRun::query()->where('company_id', $this->id)->exists();
    }
}
