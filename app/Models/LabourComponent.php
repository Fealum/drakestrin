<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabourComponent extends Model
{
    protected $table = 'dra_labour_component';

    public $timestamps = false;

    protected $casts = [
        'labour' => 'integer',
        'item' => 'integer',
        'quantity' => 'integer',
        'type' => 'integer',
    ];

    public function labourModel(): BelongsTo
    {
        return $this->belongsTo(Labour::class, 'labour');
    }

    public function itemModel(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item');
    }
}
