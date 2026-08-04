<?php

namespace App\Models\Board;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostMessage extends Model
{
    protected $primaryKey = 'post_element_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['post_element_id', 'message', 'smilies'];

    protected $casts = ['post_element_id' => 'integer', 'smilies' => 'boolean'];

    public function element(): BelongsTo
    {
        return $this->belongsTo(PostElement::class, 'post_element_id');
    }
}
