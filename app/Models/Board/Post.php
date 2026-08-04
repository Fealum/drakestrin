<?php

namespace App\Models\Board;

use App\Models\Economy\Transfer;
use App\Models\User;
use App\Models\User\Character;
use App\Support\PostElementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_id',
        'thread_id',
        'user_id',
        'character_id',
        'time',
        'message',
        'smilies',
        'ip',
    ];

    public $timestamps = false;

    protected $dateFormat = 'U';

    protected $casts = [
        'board_id' => 'integer',
        'thread_id' => 'integer',
        'user_id' => 'integer',
        'character_id' => 'integer',
        'time' => 'datetime',
        'smilies' => 'boolean',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class)->orderBy('id');
    }

    public function elements(): HasMany
    {
        return $this->hasMany(PostElement::class)->orderBy('position');
    }

    public function messages(): HasManyThrough
    {
        return $this->hasManyThrough(PostMessage::class, PostElement::class, 'post_id', 'post_element_id')->orderBy('post_elements.position');
    }

    public function messageText(string $separator = "\n\n"): string
    {
        $messages = $this->relationLoaded('elements')
            ? $this->elements->pluck('message')->filter()->pluck('message')
            : $this->messages()->pluck('message');

        $text = $messages->filter(fn ($message) => trim((string) $message) !== '')->implode($separator);

        return $text !== '' ? $text : (string) $this->message;
    }

    public function contentSummary(): string
    {
        if (trim($this->messageText()) !== '') {
            return $this->messageText();
        }

        $this->loadMissing([
            'elements.transfer.items.item',
            'elements.poll',
            'elements.sceneTransition.endedScene.location',
            'elements.sceneTransition.startedScene.location',
        ]);
        $element = $this->elements->first(fn (PostElement $element) => $element->type !== PostElementType::MESSAGE);

        return match ($element?->type) {
            PostElementType::TRANSFER => 'Handlung: '.$element->transfer?->items
                ->map(fn ($item) => $item->item?->name)->filter()->implode(', '),
            PostElementType::POLL => 'Umfrage: '.$element->poll?->question,
            PostElementType::SCENE_TRANSITION => $element->sceneTransition?->startedScene
                ? 'Szene: '.($element->sceneTransition->startedScene->location?->name ?? 'unbekannter Ort')
                : 'Szene beendet: '.($element->sceneTransition?->endedScene?->location?->name ?? 'unbekannter Ort'),
            default => 'Beitrag ohne Text',
        };
    }

    public function hasDurableElements(): bool
    {
        return $this->elements()->where('type', '<>', PostElementType::MESSAGE->value)->exists();
    }

    public function hasCharacterBoundAction(): bool
    {
        return $this->elements()->where('type', PostElementType::TRANSFER->value)->exists();
    }

    public function pageInThread(int $perPage): int
    {
        $postsBefore = self::query()
            ->where('thread_id', $this->thread_id)
            ->where(function ($query) {
                $query->where('time', '<', $this->getRawOriginal('time'))
                    ->orWhere(function ($query) {
                        $query->where('time', $this->getRawOriginal('time'))
                            ->where('id', '<=', $this->id);
                    });
            })
            ->count();

        return (int) ceil(max($postsBefore, 1) / $perPage);
    }
}
