<?php

namespace App\Models\Economy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabourComponent extends Model
{
    public $timestamps = false;

    protected $casts = [
        'labour_id' => 'integer',
        'item_id' => 'integer',
        'quantity' => 'integer',
        'type' => 'integer',
    ];

    public function labour(): BelongsTo
    {
        return $this->belongsTo(Labour::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
