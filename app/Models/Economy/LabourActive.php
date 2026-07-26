<?php

namespace App\Models\Economy;

use App\Support\ProductionPauseReason;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LabourActive extends Model
{
    protected $dateFormat = 'U';

    protected $fillable = [
        'company_worker_id',
        'labour_id',
        'since',
        'until',
        'prodas',
        'quantity',
        'instances',
        'nextinsta',
        'stop_requested_at',
        'paused_at',
        'pause_reason',
        'ended_at',
        'input_items',
        'output_items',
        'tool_items',
    ];

    public $timestamps = false;

    protected $casts = [
        'company_worker_id' => 'integer',
        'labour_id' => 'integer',
        'since' => 'datetime',
        'until' => 'datetime',
        'prodas' => 'integer',
        'quantity' => 'integer',
        'instances' => 'integer',
        'nextinsta' => 'integer',
        'stop_requested_at' => 'datetime',
        'paused_at' => 'datetime',
        'pause_reason' => ProductionPauseReason::class,
        'ended_at' => 'datetime',
        'input_items' => 'array',
        'output_items' => 'array',
        'tool_items' => 'array',
    ];

    public function companyWorker(): BelongsTo
    {
        return $this->belongsTo(CompanyWorker::class);
    }

    public function labour(): BelongsTo
    {
        return $this->belongsTo(Labour::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(ProductionRun::class)->orderByDesc('id');
    }

    public function currentRun(): HasOne
    {
        return $this->hasOne(ProductionRun::class)
            ->whereNull('completed_at')
            ->latestOfMany();
    }
}
