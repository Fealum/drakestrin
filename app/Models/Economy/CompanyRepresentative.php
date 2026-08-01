<?php

namespace App\Models\Economy;

use App\Models\User;
use App\Models\User\Character;
use App\Support\CompanyRepresentativeRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyRepresentative extends Model
{
    protected $fillable = [
        'company_id',
        'company_site_id',
        'character_id',
        'role',
        'appointed_by_user_id',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'company_site_id' => 'integer',
        'character_id' => 'integer',
        'role' => CompanyRepresentativeRole::class,
        'appointed_by_user_id' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(CompanySite::class, 'company_site_id');
    }

    public function appointedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'appointed_by_user_id');
    }
}
