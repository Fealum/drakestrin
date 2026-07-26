<?php

namespace App\Models\Economy;

use App\Models\Board\Post;
use App\Models\Board\ThreadScene;
use App\Models\User;
use App\Models\User\Character;
use App\Support\PermissionEntityType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transfer extends Model
{
    protected $fillable = [
        'post_id',
        'reversal_of_transfer_id',
        'thread_scene_id',
        'story_at',
        'created_by_user_id',
        'acted_by_character_id',
        'sender_id',
        'sender_type',
        'recipient_id',
        'recipient_type',
    ];

    protected $casts = [
        'post_id' => 'integer',
        'reversal_of_transfer_id' => 'integer',
        'thread_scene_id' => 'integer',
        'story_at' => 'integer',
        'created_by_user_id' => 'integer',
        'acted_by_character_id' => 'integer',
        'sender_id' => 'integer',
        'sender_type' => 'integer',
        'recipient_id' => 'integer',
        'recipient_type' => 'integer',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function scene(): BelongsTo
    {
        return $this->belongsTo(ThreadScene::class, 'thread_scene_id');
    }

    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversal_of_transfer_id');
    }

    public function reversal(): HasOne
    {
        return $this->hasOne(self::class, 'reversal_of_transfer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'acted_by_character_id');
    }

    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransferItem::class);
    }

    public function scopeInvolving(Builder $query, PermissionEntityType $type, int $id): Builder
    {
        return $query->where(function (Builder $query) use ($type, $id) {
            $query->where(function (Builder $query) use ($type, $id) {
                $query->where('sender_type', $type->value)->where('sender_id', $id);
            })->orWhere(function (Builder $query) use ($type, $id) {
                $query->where('recipient_type', $type->value)->where('recipient_id', $id);
            });
        });
    }
}
