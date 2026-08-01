<?php

namespace App\Models\Economy;

use App\Models\User;
use App\Models\User\Character;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyOwner extends Model
{
    protected $fillable = ['company_id', 'character_id', 'added_by_user_id'];

    protected $casts = [
        'company_id' => 'integer',
        'character_id' => 'integer',
        'added_by_user_id' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }
}
