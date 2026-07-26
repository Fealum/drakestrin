<?php

namespace App\Models\Economy;

use App\Models\Territory\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySite extends Model
{
    protected $fillable = [
        'company_id',
        'location_id',
        'name',
        'is_headquarters',
        'is_storefront',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'location_id' => 'integer',
        'is_headquarters' => 'boolean',
        'is_storefront' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
