<?php

namespace App\Models\Board;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostSceneTransition extends Model
{
    protected $primaryKey = 'post_element_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['post_element_id', 'ended_scene_id', 'started_scene_id'];

    protected $casts = ['post_element_id' => 'integer', 'ended_scene_id' => 'integer', 'started_scene_id' => 'integer'];

    public function element(): BelongsTo
    {
        return $this->belongsTo(PostElement::class, 'post_element_id');
    }

    public function endedScene(): BelongsTo
    {
        return $this->belongsTo(ThreadScene::class, 'ended_scene_id');
    }

    public function startedScene(): BelongsTo
    {
        return $this->belongsTo(ThreadScene::class, 'started_scene_id');
    }
}
