<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    public function companyWorker(): BelongsTo
    {
        return $this->belongsTo(CompanyWorker::class);
    }

    public function labour(): BelongsTo
    {
        return $this->belongsTo(Labour::class);
    }
}
