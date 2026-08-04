<?php

namespace App\Services\Board;

use App\Models\Board\Board;
use App\Models\Board\PostDraft;
use App\Models\Board\Thread;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PostDraftService
{
    public function replyState(User $user, Thread $thread): PostDraft
    {
        $draft = PostDraft::query()->where('user_id', $user->id)->where('thread_id', $thread->id)->first();
        if ($draft && ! $this->isMeaningful($draft->payload)) {
            $draft->delete();
            $draft = null;
        }

        return $draft ?? new PostDraft([
            'user_id' => $user->id,
            'thread_id' => $thread->id,
            'board_id' => $thread->board_id,
            'character_id' => $user->characters()->orderBy('name')->value('id'),
            'payload' => [['type' => 'message', 'message' => '', 'smilies' => true]],
            'version' => 0,
        ]);
    }

    public function topic(User $user, ?Board $board = null): PostDraft
    {
        return new PostDraft([
            'user_id' => $user->id,
            'board_id' => $board?->id,
            'character_id' => $user->characters()->orderBy('name')->value('id'),
            'payload' => [['type' => 'message', 'message' => '', 'smilies' => true]],
            'version' => 0,
        ]);
    }

    public function saveTopic(User $user, array $data, int $expectedVersion): PostDraft
    {
        if ($expectedVersion !== 0) {
            throw ValidationException::withMessages([
                'draft' => 'Dieser Entwurf wurde inzwischen gespeichert. Bitte öffnen Sie die gespeicherte Fassung.',
            ]);
        }

        $payload = $this->normalizePayload((array) ($data['elements'] ?? []));
        if (! $this->isMeaningful($payload) && blank($data['title'] ?? null)) {
            return new PostDraft([
                'user_id' => $user->id,
                'board_id' => filled($data['board'] ?? null) ? (int) $data['board'] : null,
                'character_id' => (int) $data['character'],
                'payload' => $payload,
                'version' => 0,
            ]);
        }

        return PostDraft::create([
            'user_id' => $user->id,
            'board_id' => filled($data['board'] ?? null) ? (int) $data['board'] : null,
            'title' => filled($data['title'] ?? null) ? trim((string) $data['title']) : null,
            'character_id' => (int) $data['character'],
            'payload' => $payload,
            'version' => 1,
        ]);
    }

    public function save(PostDraft $draft, User $user, array $data, int $expectedVersion): PostDraft
    {
        abort_unless($draft->user_id === $user->id, 403);
        $payload = $this->normalizePayload((array) ($data['elements'] ?? []));
        $updated = PostDraft::query()->whereKey($draft->id)->where('version', $expectedVersion)->update([
            'board_id' => filled($data['board'] ?? null) ? (int) $data['board'] : $draft->board_id,
            'title' => filled($data['title'] ?? null) ? trim((string) $data['title']) : null,
            'character_id' => filled($data['character'] ?? null) ? (int) $data['character'] : null,
            'payload' => json_encode($payload, JSON_THROW_ON_ERROR),
            'version' => $expectedVersion + 1,
            'updated_at' => now(),
        ]);

        if (! $updated) {
            throw ValidationException::withMessages([
                'draft' => 'Dieser Entwurf wurde inzwischen an anderer Stelle gespeichert. Die neuere Fassung wurde nicht überschrieben.',
            ]);
        }

        return $draft->fresh();
    }

    public function saveReply(Thread $thread, User $user, array $data, int $expectedVersion): PostDraft
    {
        return DB::transaction(function () use ($thread, $user, $data, $expectedVersion) {
            $draft = PostDraft::query()->where('user_id', $user->id)->where('thread_id', $thread->id)->lockForUpdate()->first();

            if (($draft?->version ?? 0) !== $expectedVersion) {
                throw ValidationException::withMessages([
                    'draft' => 'Dieser Entwurf wurde inzwischen an anderer Stelle gespeichert. Die neuere Fassung wurde nicht überschrieben.',
                ]);
            }

            $payload = $this->normalizePayload((array) ($data['elements'] ?? []));
            if (! $this->isMeaningful($payload)) {
                $draft?->delete();

                return $this->replyState($user, $thread);
            }

            if (! $draft) {
                return PostDraft::create([
                    'user_id' => $user->id,
                    'thread_id' => $thread->id,
                    'board_id' => $thread->board_id,
                    'character_id' => (int) $data['character'],
                    'payload' => $payload,
                    'version' => 1,
                ]);
            }

            return $this->save($draft, $user, $data, $expectedVersion);
        });
    }

    public function isMeaningful(array $payload): bool
    {
        return collect($payload)->contains(fn (array $element) => ($element['type'] ?? null) !== 'message'
            || trim((string) ($element['message'] ?? '')) !== '');
    }

    public function appendQuote(array $payload, string $quote): array
    {
        $index = collect($payload)->keys()->filter(fn (int $index) => ($payload[$index]['type'] ?? null) === 'message')->last();

        if ($index === null) {
            $payload[] = ['type' => 'message', 'message' => $quote, 'smilies' => true];

            return $payload;
        }

        $current = rtrim((string) ($payload[$index]['message'] ?? ''));
        $payload[$index]['message'] = $current === '' ? $quote : $current."\n".$quote;

        return $payload;
    }

    public function command(array $payload, string $intent, ?int $index = null, ?int $target = null, ?int $optionIndex = null): array
    {
        $payload = array_values($payload);

        if (in_array($intent, ['add_message', 'add_transfer', 'add_scene_start', 'add_scene_end', 'add_poll'], true)) {
            $payload[] = match ($intent) {
                'add_message' => ['type' => 'message', 'message' => '', 'smilies' => true],
                'add_transfer' => ['type' => 'transfer', 'scene_key' => null, 'transfer_action' => '', 'inventory' => []],
                'add_scene_start' => ['type' => 'scene_transition', 'scene_action' => 'start', 'scene_key' => (string) Str::uuid(), 'story_at' => now()->timestamp],
                'add_scene_end' => ['type' => 'scene_transition', 'scene_action' => 'end', 'story_at' => now()->timestamp],
                'add_poll' => ['type' => 'poll', 'question' => '', 'options' => ['', ''], 'visibility' => 'anonymous', 'max_choices' => 1],
                default => throw ValidationException::withMessages(['elements' => 'Unbekannter Baustein.']),
            };
        } elseif ($intent === 'remove' && $index !== null && isset($payload[$index])) {
            array_splice($payload, $index, 1);
        } elseif ($intent === 'move' && $index !== null && $target !== null && isset($payload[$index])) {
            $element = array_splice($payload, $index, 1)[0];
            array_splice($payload, max(0, min($target, count($payload))), 0, [$element]);
        } elseif ($intent === 'add_poll_option' && $index !== null && ($payload[$index]['type'] ?? null) === 'poll') {
            $payload[$index]['options'][] = '';
        } elseif ($intent === 'remove_poll_option' && $index !== null && $optionIndex !== null && ($payload[$index]['type'] ?? null) === 'poll') {
            if (count($payload[$index]['options'] ?? []) <= 2) {
                throw ValidationException::withMessages(['elements.'.$index.'.options' => 'Eine Umfrage braucht mindestens zwei Antworten.']);
            }
            array_splice($payload[$index]['options'], $optionIndex, 1);
            $payload[$index]['options'] = array_values($payload[$index]['options']);
            $payload[$index]['max_choices'] = min(
                (int) ($payload[$index]['max_choices'] ?? 1),
                count($payload[$index]['options']),
            );
        }

        return array_values($payload);
    }

    public function normalizePayload(array $payload): array
    {
        return collect($payload)->values()->map(function (array $element) {
            if (($element['type'] ?? null) === 'scene_transition') {
                foreach (['story_at'] as $field) {
                    if (isset($element[$field]) && ! is_numeric($element[$field])) {
                        $element[$field] = $this->timestamp($element[$field]);
                    }
                }
            }
            if (($element['type'] ?? null) === 'poll' && filled($element['closes_at'] ?? null) && ! is_numeric($element['closes_at'])) {
                $element['closes_at'] = $this->timestamp($element['closes_at']);
            }

            return $element;
        })->all();
    }

    public function bindAndValidateScenes(array $payload, ?Thread $thread): array
    {
        $activeKey = $thread?->currentScene?->id ? 'scene:'.$thread->currentScene->id : null;

        foreach ($payload as $index => &$element) {
            $type = $element['type'] ?? null;
            if ($type === 'poll' && $activeKey !== null) {
                throw ValidationException::withMessages(['elements.'.$index => 'Eine Umfrage kann nicht innerhalb einer Szene stehen.']);
            }
            if ($type === 'transfer') {
                if ($activeKey === null) {
                    throw ValidationException::withMessages(['elements.'.$index => 'Eine Handlung kann nur innerhalb einer Szene stehen.']);
                }
                if (filled($element['scene_key'] ?? null) && $element['scene_key'] !== $activeKey) {
                    throw ValidationException::withMessages(['elements.'.$index => 'Eine Handlung kann ihre Szene nicht verlassen.']);
                }
                $element['scene_key'] = $activeKey;
            }
            if ($type !== 'scene_transition') {
                continue;
            }
            if (($element['scene_action'] ?? 'start') === 'end') {
                if ($activeKey === null) {
                    throw ValidationException::withMessages(['elements.'.$index => 'An dieser Position ist keine Szene aktiv.']);
                }
                $activeKey = null;
            } else {
                $element['scene_key'] = $element['scene_key'] ?? (string) Str::uuid();
                $activeKey = $element['scene_key'];
            }
        }

        return $payload;
    }

    public function endsInsideScene(array $payload, ?Thread $thread): bool
    {
        $active = (bool) $thread?->currentScene;

        foreach ($payload as $element) {
            if (($element['type'] ?? null) !== 'scene_transition') {
                continue;
            }
            $active = ($element['scene_action'] ?? 'start') !== 'end';
        }

        return $active;
    }

    /** @return array<int, list<int>> */
    public function validMoveTargets(array $payload, ?Thread $thread): array
    {
        $targets = [];

        foreach (array_keys($payload) as $index) {
            foreach (array_keys($payload) as $target) {
                try {
                    $candidate = $this->command($payload, 'move', $index, $target);
                    $this->bindAndValidateScenes($candidate, $thread);
                    $targets[$index][] = $target;
                } catch (ValidationException) {
                    // This destination would violate a scene boundary.
                }
            }
        }

        return $targets;
    }

    private function timestamp(?string $value): ?int
    {
        return filled($value) ? CarbonImmutable::createFromFormat('Y-m-d\TH:i', $value, config('app.timezone'))->timestamp : null;
    }
}
