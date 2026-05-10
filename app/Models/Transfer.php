<?php

namespace App\Models;

use App\Models\Board\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transfer extends Model
{
    protected $table = 'dra_transfer';

    public $timestamps = false;

    protected $fillable = [
        'post',
        'sender',
        'table__sender',
        'recipient',
        'table__recipient',
    ];

    protected $casts = [
        'post' => 'integer',
        'sender' => 'integer',
        'table__sender' => 'integer',
        'recipient' => 'integer',
        'table__recipient' => 'integer',
    ];

    public function postModel(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post');
    }

    public function senderCharacter(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'sender');
    }

    public function recipientCharacter(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'recipient');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransferItem::class, 'transfer');
    }
}
