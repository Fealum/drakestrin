<?php

namespace App\Models\Economy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionRun extends Model
{
    public $timestamps = false;

    protected $dateFormat = 'U';

    protected $fillable = [
        'labour_active_id',
        'company_id',
        'company_worker_id',
        'labour_id',
        'labour_name',
        'worker_name',
        'instances',
        'output_state',
        'inputs',
        'outputs',
        'started_at',
        'due_at',
        'completed_at',
    ];

    protected $casts = [
        'labour_active_id' => 'integer',
        'company_id' => 'integer',
        'company_worker_id' => 'integer',
        'labour_id' => 'integer',
        'instances' => 'integer',
        'output_state' => 'integer',
        'inputs' => 'array',
        'outputs' => 'array',
        'started_at' => 'datetime',
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function activeLabour(): BelongsTo
    {
        return $this->belongsTo(LabourActive::class, 'labour_active_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(CompanyWorker::class, 'company_worker_id');
    }

    public function labour(): BelongsTo
    {
        return $this->belongsTo(Labour::class);
    }
}
