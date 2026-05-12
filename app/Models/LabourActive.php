<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabourActive extends Model
{
    protected $table = 'dra_labour_active';

    protected $dateFormat = 'U';

    protected $fillable = [
        'company_worker',
        'labour',
        'since',
        'until',
        'prodas',
        'quantity',
        'instances',
        'nextinsta',
    ];

    public $timestamps = false;

    protected $casts = [
        'company_worker' => 'integer',
        'labour' => 'integer',
        'since' => 'datetime',
        'until' => 'datetime',
        'prodas' => 'integer',
        'quantity' => 'integer',
        'instances' => 'integer',
        'nextinsta' => 'integer',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(CompanyWorker::class, 'company_worker');
    }

    public function labourModel(): BelongsTo
    {
        return $this->belongsTo(Labour::class, 'labour');
    }
}
