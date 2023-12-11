<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dra_user';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    const CREATED_AT = 'regdate';
    const UPDATED_AT = 'lastvisit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'character__avatar',
        'usertext',
        'regdate',
        'lastvisit',
        'lastactivity',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<int, string>
     */
    protected $casts = [
        'character__avatar' => 'integer',
        'regdate' => 'datetime',
        'lastvisit' => 'datetime',
        'lastactivity' => 'datetime',
    ];

    public function character_avatar(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'character__avatar');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'dra_group2user', 'user', 'group');
    }
}
