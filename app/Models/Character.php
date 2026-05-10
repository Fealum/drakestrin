<?php

namespace App\Models;

use App\Models\Board\Post;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Character extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dra_character';

    protected $fillable = [
        'name',
        'regdate',
        'user',
        'usertext',
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
        'post__total' => 'integer',
        'regdate' => 'datetime',
        'avatar' => 'integer',
        'gender' => 'integer',
        'user' => 'integer',
    ];

    public function userModel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'character');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class, 'owner')
            ->where('table__owner', 6)
            ->with('itemModel')
            ->orderBy('id');
    }

    public function avatarThumbPath(): string
    {
        if ($this->avatar) {
            return (string) $this->id;
        }

        $firstCharacter = mb_substr($this->name, 0, 1);

        return ctype_alpha($firstCharacter) ? 'i/' . mb_strtolower($firstCharacter) : 'i/_';
    }
}
