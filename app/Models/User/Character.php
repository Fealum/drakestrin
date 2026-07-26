<?php

namespace App\Models\User;

use App\Models\Board\Post;
use App\Models\Economy\Company;
use App\Models\Economy\CompanyRepresentative;
use App\Models\Economy\Inventory;
use App\Models\Territory\Territory;
use App\Models\User as Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'regdate',
        'user_id',
        'usertext',
        'birthday',
        'avatar',
        'interests',
        'location',
        'post_count',
        'work',
        'gender',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    protected $casts = [
        'post_count' => 'integer',
        'regdate' => 'datetime',
        'birthday' => 'integer',
        'avatar' => 'integer',
        'gender' => 'integer',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function inventory(): MorphMany
    {
        return $this->morphMany(Inventory::class, 'owner')
            ->with('item')
            ->orderBy('id');
    }

    public function territories(): HasMany
    {
        return $this->hasMany(Territory::class)
            ->orderBy('type')
            ->orderByRaw('LOWER(name)');
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class)
            ->orderByRaw('LOWER(name)');
    }

    public function companyRepresentations(): HasMany
    {
        return $this->hasMany(CompanyRepresentative::class);
    }

    public function avatarThumbPath(): string
    {
        if ($this->avatar) {
            return (string) $this->id;
        }

        $firstCharacter = mb_substr($this->name, 0, 1);

        return ctype_alpha($firstCharacter) ? 'i/'.mb_strtolower($firstCharacter) : 'i/_';
    }

    public function avatarThumbUrl(): string
    {
        return Storage::disk('public')->url('character-avatars/thumb/'.$this->avatarThumbPath().'.jpg');
    }

    public function avatarUrl(): string
    {
        return Storage::disk('public')->url('character-avatars/'.($this->avatar ? $this->id : $this->avatarThumbPath()).'.jpg');
    }

    public function postsPerDay(): float
    {
        $days = max(1, now()->diffInSeconds($this->regdate ?: now()) / 86400);

        return ($this->post_count ?? 0) / $days;
    }
}
