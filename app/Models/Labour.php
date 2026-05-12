<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Labour extends Model
{
    protected $table = 'dra_labour';

    public $timestamps = false;

    protected $casts = [
        'type' => 'integer',
        'duration' => 'integer',
        'workload' => 'integer',
    ];

    public function components(): HasMany
    {
        return $this->hasMany(LabourComponent::class, 'labour')
            ->with('itemModel')
            ->orderBy('type')
            ->orderBy('id');
    }
}
