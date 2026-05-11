<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Board\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

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
        'birthday',
        'interests',
        'location',
        'work',
        'gender',
        'post__total',
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
        'birthday' => 'integer',
        'gender' => 'integer',
        'post__total' => 'integer',
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
        return $this->belongsToMany(Group::class, 'dra_group2user', 'user', 'group')
            ->orderBy('priority')
            ->orderByRaw('LOWER(name)');
    }

    public function permissions(): MorphMany
    {
        return $this->morphMany(Permission::class, 'recipient_legacy', 'table__recipient', 'recipient');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_user_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_user_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user');
    }

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class, 'user')
            ->orderByDesc('post__total')
            ->orderByRaw('LOWER(name)');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(UserContact::class, 'user')
            ->with('protocolModel')
            ->orderBy('protocol')
            ->orderBy('contact');
    }

    public function avatarThumbPath(): string
    {
        if ($this->character__avatar) {
            return (string) $this->character__avatar;
        }

        $firstCharacter = mb_substr($this->name, 0, 1);

        return ctype_alpha($firstCharacter) ? 'i/' . mb_strtolower($firstCharacter) : 'i/_';
    }

    public function avatarPath(): string
    {
        return $this->avatarThumbPath();
    }

    public function avatarThumbUrl(): string
    {
        return Storage::disk('public')->url('character-avatars/thumb/' . $this->avatarThumbPath() . '.jpg');
    }

    public function avatarUrl(): string
    {
        return Storage::disk('public')->url('character-avatars/' . $this->avatarPath() . '.jpg');
    }

    public function postsPerDay(): float
    {
        $days = max(1, now()->diffInSeconds($this->regdate ?: now()) / 86400);

        return ($this->post__total ?? 0) / $days;
    }
}
