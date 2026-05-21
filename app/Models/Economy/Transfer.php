<?php

namespace App\Models\Economy;

use App\Models\Board\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transfer extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'sender_id',
        'sender_type',
        'recipient_id',
        'recipient_type',
    ];

    protected $casts = [
        'post_id' => 'integer',
        'sender_id' => 'integer',
        'sender_type' => 'integer',
        'recipient_id' => 'integer',
        'recipient_type' => 'integer',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
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
}
