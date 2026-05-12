<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyWorker extends Model
{
    protected $table = 'dra_company_worker';

    protected $dateFormat = 'U';

    protected $fillable = [
        'name',
        'type',
        'company',
        'hired',
        'paid',
    ];

    public $timestamps = false;

    protected $casts = [
        'type' => 'integer',
        'company' => 'integer',
        'hired' => 'datetime',
        'paid' => 'datetime',
    ];

    public function companyModel(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company');
    }

    public function activeLabours(): HasMany
    {
        return $this->hasMany(LabourActive::class, 'company_worker')
            ->with('labourModel.components.itemModel')
            ->orderBy('since');
    }

    public function salaryStatus(): ?string
    {
        $months = (int) floor((now()->timestamp - ($this->paid?->timestamp ?? now()->timestamp)) / 2592000);

        return match (true) {
            $months > 4 => $months . ' Monate ohne Gehalt',
            $months > 3 => 'im Streik',
            $months > 2 => 'überfällig',
            $months > 1 => 'fällig',
            default => null,
        };
    }
}
