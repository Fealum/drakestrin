<?php

namespace App\Models\Territory;

use App\Models\Concerns\HasSpatialGeometry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Settlement extends Model
{
    use HasSpatialGeometry;

    protected $table = 'dra_settlement';

    protected $fillable = [
        'name',
        'population',
        'priority',
    ];

    public $timestamps = false;

    protected $casts = [
        'population' => 'integer',
        'priority' => 'integer',
    ];

    public function territories(): HasMany
    {
        return $this->hasMany(Territory::class, 'settlement');
    }
}
