<?php

namespace App\Models\Board;

use App\Models\Economy\Transfer;
use App\Support\PostElementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PostElement extends Model
{
    protected $fillable = ['post_id', 'position', 'type'];

    protected $casts = [
        'post_id' => 'integer',
        'position' => 'integer',
        'type' => PostElementType::class,
    ];

    protected static function booted(): void
    {
        static::updating(function (self $element) {
            if ($element->isDirty(['post_id', 'position', 'type'])) {
                throw new \LogicException('Published post element identity and order are immutable.');
            }
        });
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function message(): HasOne
    {
        return $this->hasOne(PostMessage::class);
    }

    public function transfer(): HasOne
    {
        return $this->hasOne(Transfer::class);
    }

    public function sceneTransition(): HasOne
    {
        return $this->hasOne(PostSceneTransition::class);
    }

    public function poll(): HasOne
    {
        return $this->hasOne(Poll::class);
    }
}
