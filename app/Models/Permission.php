<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Permission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dra_permission';

    protected $fillable = [
        'table__recipient',
        'recipient',
        'table__subject',
        'subject',
        'permit',
        'value',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function recipient_legacy(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'table__recipient', 'recipient');
    }

    public function subject_legacy(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'table__subject', 'subject');
    }

    public function permit_legacy(): BelongsTo
    {
        return $this->belongsTo(Permit::class, 'permit');
    }
}
