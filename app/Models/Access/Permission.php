<?php

namespace App\Models\Access;

use App\Support\PermissionEntityType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_type',
        'recipient_id',
        'subject_type',
        'subject_id',
        'permit_id',
        'value',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function permit(): BelongsTo
    {
        return $this->belongsTo(Permit::class);
    }

    public function recipientName(): string
    {
        if (PermissionEntityType::fromDatabase($this->recipient_type) === PermissionEntityType::USER && (int) $this->recipient_id === 0) {
            return 'Alle';
        }

        return $this->recipient?->name ?? ('#' . $this->recipient_id);
    }
}
