<?php

namespace App\Models\Territory;

use App\Models\Concerns\HasSpatialGeometry;
use App\Models\Board\ThreadScene;
use App\Models\Economy\Inventory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Location extends Model
{
    use HasFactory, HasSpatialGeometry;

    protected $fillable = [
        'parent_type',
        'parent_id',
        'created_by_user_id',
        'name',
        'description',
        'priority',
    ];

    protected $casts = [
        'parent_type' => 'integer',
        'parent_id' => 'integer',
        'created_by_user_id' => 'integer',
        'priority' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function parent(): MorphTo
    {
        return $this->morphTo();
    }

    public function children(): MorphMany
    {
        return $this->morphMany(self::class, 'parent')
            ->orderBy('priority')
            ->orderByRaw('LOWER(name)');
    }

    public function inventory(): MorphMany
    {
        return $this->morphMany(Inventory::class, 'owner')
            ->with('item')
            ->orderBy('id');
    }

    public function threadScenes()
    {
        return $this->hasMany(ThreadScene::class)
            ->with('thread')
            ->orderByDesc('created_at');
    }
}
