<?php

namespace App\Services\Board;

use App\Models\Board\Board;
use App\Models\Board\PostDraft;
use App\Models\Board\Thread;
use App\Models\User;
use App\Repositories\Board\ThreadParticipantRepository;
use App\Services\PermissionService;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class PostDraftService
{
    public function __construct(
        private ThreadParticipantRepository $participants,
        private PostMarkerParser $markers,
        private PermissionService $permissions,
    ) {}

    public function replyState(User $user, Thread $thread): PostDraft
    {
        $draft = PostDraft::query()->where('user_id', $user->id)->where('thread_id', $thread->id)->first();
        if ($draft && ! $this->isMeaningful($draft->payload) && blank($draft->new_character_name)) {
            $draft->delete();
            $draft = null;
        }

        return $draft ?? new PostDraft([
            'user_id' => $user->id,
            'thread_id' => $thread->id,
            'board_id' => $thread->board_id,
            'character_id' => $this->participants->postingCharacters($user, $thread)->first()?->id,
            'creates_character' => false,
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
            'creates_character' => false,
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

        $payload = $this->mergeAdjacentMessages($this->withoutEmptyMessages($this->normalizePayload((array) ($data['elements'] ?? []))));
        if (! $this->isMeaningful($payload) && blank($data['title'] ?? null) && blank($data['newcharname'] ?? null)) {
            return new PostDraft([
                'user_id' => $user->id,
                'board_id' => filled($data['board'] ?? null) ? (int) $data['board'] : null,
                ...$this->characterAttributes($data),
                'payload' => $payload,
                'version' => 0,
            ]);
        }

        return PostDraft::create([
            'user_id' => $user->id,
            'board_id' => filled($data['board'] ?? null) ? (int) $data['board'] : null,
            'title' => filled($data['title'] ?? null) ? trim((string) $data['title']) : null,
            ...$this->characterAttributes($data),
            'payload' => $payload,
            'version' => 1,
        ]);
    }

    public function save(PostDraft $draft, User $user, array $data, int $expectedVersion): PostDraft
    {
        abort_unless($draft->user_id === $user->id, 403);
        $payload = $this->mergeAdjacentMessages($this->withoutEmptyMessages($this->normalizePayload((array) ($data['elements'] ?? []))));
        $updated = PostDraft::query()->whereKey($draft->id)->where('version', $expectedVersion)->update([
            'board_id' => filled($data['board'] ?? null) ? (int) $data['board'] : $draft->board_id,
            'title' => filled($data['title'] ?? null) ? trim((string) $data['title']) : null,
            ...$this->characterAttributes($data),
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

            $payload = $this->mergeAdjacentMessages($this->withoutEmptyMessages($this->normalizePayload((array) ($data['elements'] ?? []))));
            if (! $this->isMeaningful($payload) && blank($data['newcharname'] ?? null)) {
                $draft?->delete();

                return $this->replyState($user, $thread)->fill([
                    ...$this->characterAttributes($data),
                    'payload' => $payload,
                ]);
            }

            if (! $draft) {
                return PostDraft::create([
                    'user_id' => $user->id,
                    'thread_id' => $thread->id,
                    'board_id' => $thread->board_id,
                    ...$this->characterAttributes($data),
                    'payload' => $payload,
                    'version' => 1,
                ]);
            }

            return $this->save($draft, $user, $data, $expectedVersion);
        });
    }

    public function publish(PostDraft $draft, Closure $publish): mixed
    {
        return DB::transaction(function () use ($draft, $publish) {
            $current = PostDraft::query()->whereKey($draft->id)->lockForUpdate()->first();
            if (! $current || $current->version !== $draft->version) {
                throw ValidationException::withMessages([
                    'draft' => 'Dieser Entwurf wurde inzwischen geändert. Bitte öffne die neuere Fassung.',
                ]);
            }

            return $publish();
        });
    }

    public function isMeaningful(array $payload): bool
    {
        return collect($payload)->contains(fn (array $element) => ($element['type'] ?? null) !== 'message'
            || trim((string) ($element['message'] ?? '')) !== '');
    }

    public function withoutEmptyMessages(array $payload): array
    {
        return array_values(array_filter($payload, fn (array $element) => ! $this->isEmptyMessage($element)));
    }

    private function isEmptyMessage(array $element): bool
    {
        return ($element['type'] ?? null) === 'message' && trim((string) ($element['message'] ?? '')) === '';
    }

    public function withTextSlots(array $payload): array
    {
        $elements = [];
        foreach ($this->withoutEmptyMessages($payload) as $element) {
            if (($element['type'] ?? null) !== 'message' && ($elements[array_key_last($elements)]['type'] ?? null) !== 'message') {
                $elements[] = ['type' => 'message', 'message' => '', 'smilies' => true];
            }
            $elements[] = $element;
        }
        if (($elements[array_key_last($elements)]['type'] ?? null) !== 'message') {
            $elements[] = ['type' => 'message', 'message' => '', 'smilies' => true];
        }

        return $elements;
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
            $position = $target === null ? count($payload) : max(0, min($target, count($payload)));
            if ($intent === 'add_message' && $this->hasAdjacentMessage($payload, $position)) {
                return $payload;
            }
            $element = match ($intent) {
                'add_message' => ['type' => 'message', 'message' => '', 'smilies' => true],
                'add_transfer' => ['type' => 'transfer', 'scene_key' => null, 'source' => 'character', 'recipient_party' => '', 'inventory' => []],
                'add_scene_start' => ['type' => 'scene_transition', 'scene_action' => 'start', 'scene_key' => (string) Str::uuid(), 'story_at' => now()->startOfMinute()->timestamp],
                'add_scene_end' => ['type' => 'scene_transition', 'scene_action' => 'end', 'story_at' => now()->startOfMinute()->timestamp],
                'add_poll' => ['type' => 'poll', 'question' => '', 'options' => ['', ''], 'visibility' => 'anonymous', 'max_choices' => 1],
                default => throw ValidationException::withMessages(['elements' => 'Unbekannter Baustein.']),
            };
            array_splice($payload, $position, 0, [$element]);
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

    public function convertMarkers(array $payload, ?int $index, User $user, ?Thread $thread, ?int $boardId = null): array
    {
        if ($index !== null && ($payload[$index]['type'] ?? null) !== 'message') {
            throw ValidationException::withMessages(['elements' => 'Bitte wähle einen Text für die Bausteine.']);
        }
        $active = (bool) $thread?->currentScene;
        $target = $thread ?? new Thread(['board_id' => $boardId ?? 0]);
        $expanded = [];
        foreach ($payload as $messageIndex => $message) {
            if (($message['type'] ?? null) !== 'message' || ($index !== null && $index !== $messageIndex)) {
                $expanded[] = $message;
                if (($message['type'] ?? null) === 'scene_transition') {
                    $active = ($message['scene_action'] ?? 'start') !== 'end';
                }

                continue;
            }
            foreach ($this->markerSegments($message, $messageIndex) as $segment) {
                if (isset($segment['text'])) {
                    $expanded[] = [...$message, 'message' => $segment['text']];

                    continue;
                }
                $intent = match ($segment['marker']) {
                    'action', 'transfer' => 'add_transfer',
                    'poll' => 'add_poll',
                    'scene' => $active ? 'add_scene_end' : 'add_scene_start',
                };
                $permit = match ($intent) {
                    'add_transfer' => 'transfer',
                    'add_poll' => 'createpoll',
                    'add_scene_start' => 'setthreadscene',
                    'add_scene_end' => 'endthreadscene',
                };
                // The exact scene operation is resolved below from the following elements.
                // Check scene access here and the required operation again before returning.
                $allowed = $segment['marker'] === 'scene'
                    ? ($this->permissions->allows('setthreadscene', $target, $user) || $this->permissions->allows('endthreadscene', $target, $user))
                    : $this->permissions->allows($permit, $target, $user);
                if (! $allowed) {
                    throw ValidationException::withMessages(['elements.'.$messageIndex.'.message' => 'Du darfst diesen Baustein hier nicht erstellen.']);
                }
                // Text slots change indices; retain a UI identity for focus after insertion.
                $expanded[] = [...$this->command([], $intent)[0], 'editor_key' => (string) Str::uuid()];
                if ($segment['marker'] === 'scene') {
                    $active = ! $active;
                }
            }
        }

        // New boundaries may deliberately change the context of following draft transfers.
        // Existing boundaries and ordinary reordering still enforce immutable scene bindings.
        $existingKeys = array_column(array_filter($payload, fn ($element) => ($element['type'] ?? null) === 'scene_transition'), 'editor_key');
        foreach ($expanded as $position => $element) {
            if (($element['type'] ?? null) !== 'scene_transition' || ! isset($element['editor_key']) || in_array($element['editor_key'], $existingKeys, true)) {
                continue;
            }
            $prefix = array_slice($expanded, 0, $position);
            $state = $this->sceneStates($prefix, $thread)['positions'][$position];
            $action = $state['active'] ? 'end' : 'start';
            for ($next = $position + 1; $next < count($expanded); $next++) {
                if (($expanded[$next]['type'] ?? null) === 'scene_transition') {
                    break;
                }
                if (($expanded[$next]['type'] ?? null) === 'transfer') {
                    $action = 'start';
                }
            }
            if (! $this->permissions->allows($action === 'start' ? 'setthreadscene' : 'endthreadscene', $target, $user)) {
                throw ValidationException::withMessages(['elements' => 'Du darfst diesen Szenenbaustein hier nicht erstellen.']);
            }
            $expanded[$position]['scene_action'] = $action;
            if ($action === 'start') {
                $expanded[$position]['scene_key'] ??= (string) Str::uuid();
                for ($next = $position + 1; $next < count($expanded); $next++) {
                    if (($expanded[$next]['type'] ?? null) === 'scene_transition') {
                        break;
                    }
                    if (($expanded[$next]['type'] ?? null) === 'transfer') {
                        $expanded[$next]['scene_key'] = $expanded[$position]['scene_key'];
                    }
                }
            }
        }

        return $this->bindAndValidateScenes($expanded, $thread);
    }

    public function applyPendingEdits(array $payload, array $positions, array $removals): array
    {
        foreach (array_unique([...array_keys($positions), ...array_keys($removals)]) as $index) {
            if (! ctype_digit((string) $index) || ! isset($payload[$index]) || ($payload[$index]['type'] ?? null) === 'message') {
                throw ValidationException::withMessages(['elements' => 'Unbekannter Baustein für die Positionsänderung oder Entfernung.']);
            }
        }

        $ordered = [];
        foreach ($payload as $index => $element) {
            if ($removals[$index] ?? false) {
                continue;
            }
            $position = (int) ($positions[$index] ?? $index);
            if ($position < 0 || $position >= count($payload)) {
                throw ValidationException::withMessages(['elements.'.$index => 'Diese Position ist für den Baustein nicht zulässig.']);
            }
            $ordered[] = ['element' => $element, 'order' => [$position, $position <=> $index, $index]];
        }
        usort($ordered, fn (array $left, array $right) => $left['order'] <=> $right['order']);

        return array_column($ordered, 'element');
    }

    public function containsMarkers(array $payload): bool
    {
        foreach ($payload as $index => $element) {
            if (($element['type'] ?? null) !== 'message') {
                continue;
            }
            foreach ($this->markerSegments($element, $index) as $segment) {
                if (isset($segment['marker'])) {
                    return true;
                }
            }
        }

        return false;
    }

    public function assertMarkersConverted(array $payload): void
    {
        foreach ($payload as $index => $element) {
            if (($element['type'] ?? null) !== 'message') {
                continue;
            }
            foreach ($this->markerSegments($element, $index) as $segment) {
                if (isset($segment['marker'])) {
                    throw ValidationException::withMessages([
                        'elements.'.$index.'.message' => 'Bitte übernimm zuerst die Bausteine im Text und fülle sie aus.',
                    ]);
                }
            }
        }
    }

    private function markerSegments(array $message, int $index): array
    {
        try {
            return $this->markers->segments((string) ($message['message'] ?? ''));
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['elements.'.$index.'.message' => $exception->getMessage()]);
        }
    }

    public function mergeAdjacentMessages(array $payload): array
    {
        $merged = [];
        foreach ($payload as $element) {
            $last = array_key_last($merged);
            if ($last !== null && ($element['type'] ?? null) === 'message'
                && ($merged[$last]['type'] ?? null) === 'message'
                && (bool) ($element['smilies'] ?? true) === (bool) ($merged[$last]['smilies'] ?? true)) {
                $before = (string) ($merged[$last]['message'] ?? '');
                $after = (string) ($element['message'] ?? '');
                $merged[$last]['message'] = $before.($before !== '' && $after !== '' ? "\n\n" : '').$after;
            } else {
                $merged[] = $element;
            }
        }

        return $merged;
    }

    private function hasAdjacentMessage(array $payload, int $position): bool
    {
        return ($payload[$position - 1]['type'] ?? null) === 'message'
            || ($payload[$position]['type'] ?? null) === 'message';
    }

    public function normalizePayload(array $payload): array
    {
        return collect($payload)->values()->map(function (array $element) {
            if (($element['type'] ?? null) === 'scene_transition') {
                foreach (['story_at'] as $field) {
                    if (array_key_exists($field, $element) && blank($element[$field])) {
                        $element[$field] = null;
                    } elseif (isset($element[$field]) && ! is_numeric($element[$field])) {
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
                    throw ValidationException::withMessages(['elements.'.$index => 'Eine Übergabe kann nur innerhalb einer Szene stehen.']);
                }
                if (filled($element['scene_key'] ?? null) && $element['scene_key'] !== $activeKey) {
                    throw ValidationException::withMessages(['elements.'.$index => 'Eine Übergabe kann ihre Szene nicht verlassen.']);
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

    /** @return array{positions: array<int, array>, elements: array<int, array>} */
    public function sceneStates(array $payload, ?Thread $thread): array
    {
        $scene = $thread?->currentScene;
        $state = [
            'active' => (bool) $scene,
            'key' => $scene ? 'scene:'.$scene->id : null,
            'location_id' => $scene?->location_id,
            'location_name' => $scene?->location?->name,
            'story_at' => $scene?->story_started_at,
        ];
        $positions = [0 => $state];
        $elements = [];

        foreach (array_values($payload) as $index => $element) {
            $elements[$index] = $state;
            if (($element['type'] ?? null) === 'scene_transition') {
                if (($element['scene_action'] ?? 'start') === 'end') {
                    $state = ['active' => false, 'key' => null, 'location_id' => null, 'location_name' => null, 'story_at' => null];
                } else {
                    $state = [
                        'active' => true,
                        'key' => $element['scene_key'] ?? null,
                        'location_id' => filled($element['location_id'] ?? null) ? (int) $element['location_id'] : null,
                        'location_name' => null,
                        'story_at' => $this->displayTimestamp($element['story_at'] ?? null),
                    ];
                }
            }
            $positions[$index + 1] = $state;
        }

        return compact('positions', 'elements');
    }

    /** @return array<int, list<string>> */
    public function validInsertions(array $payload, ?Thread $thread): array
    {
        $states = $this->sceneStates($payload, $thread)['positions'];
        $insertions = [];

        foreach (range(0, count($payload)) as $position) {
            $state = $states[$position];
            $insertions[$position] = [];
            $candidates = ['add_scene_start'];
            if (! $this->hasAdjacentMessage($payload, $position)) {
                $candidates[] = 'add_message';
            }
            if ($state['active']) {
                $candidates[] = 'add_scene_end';
                if ($state['story_at'] !== null) {
                    $candidates[] = 'add_transfer';
                }
            } else {
                $candidates[] = 'add_poll';
            }

            foreach ($candidates as $intent) {
                try {
                    $this->bindAndValidateScenes($this->command($payload, $intent, target: $position), $thread);
                    $insertions[$position][] = $intent;
                } catch (ValidationException) {
                    // The remainder of the composition needs the current scene state.
                }
            }
        }

        return $insertions;
    }

    /** @return array<int, bool> */
    public function removableElements(array $payload, ?Thread $thread): array
    {
        return collect(array_keys($payload))->mapWithKeys(function (int $index) use ($payload, $thread) {
            try {
                $this->bindAndValidateScenes($this->command($payload, 'remove', $index), $thread);

                return [$index => true];
            } catch (ValidationException) {
                return [$index => false];
            }
        })->all();
    }

    /** @return array<int, list<string>> */
    public function validSceneActions(array $payload, ?Thread $thread): array
    {
        $states = $this->sceneStates($payload, $thread)['elements'];
        $choices = [];

        foreach ($payload as $index => $element) {
            if (($element['type'] ?? null) !== 'scene_transition') {
                continue;
            }
            foreach (['start', 'end'] as $action) {
                if ($action === 'end' && ! $states[$index]['active']) {
                    continue;
                }
                $candidate = $payload;
                $candidate[$index]['scene_action'] = $action;
                if ($action === 'start') {
                    $candidate[$index]['scene_key'] = $candidate[$index]['scene_key'] ?? (string) Str::uuid();
                }
                try {
                    $this->bindAndValidateScenes($candidate, $thread);
                    $choices[$index][] = $action;
                } catch (ValidationException) {
                    // This action would invalidate a later element.
                }
            }
        }

        return $choices;
    }

    /** @return array<int, list<int>> */
    public function validMoveTargets(array $payload, ?Thread $thread): array
    {
        $targets = [];
        $movable = $payload;

        foreach (array_keys($payload) as $index) {
            if (($payload[$index]['type'] ?? null) === 'message') {
                $targets[$index] = [$index];

                continue;
            }
            $original = $this->withoutEmptyMessages($movable);
            foreach (array_keys($payload) as $target) {
                try {
                    $candidate = $this->command($movable, 'move', $index, $target);
                    if ($index !== $target && $this->mergeAdjacentMessages($this->withoutEmptyMessages($candidate)) === $this->mergeAdjacentMessages($original)) {
                        continue;
                    }
                    $this->bindAndValidateScenes($candidate, $thread);
                    $targets[$index][] = $target;
                } catch (ValidationException) {
                    // This destination would violate a scene boundary.
                }
            }
        }

        return $targets;
    }

    public function markerOptions(array $payload, ?Thread $thread): array
    {
        $states = $this->sceneStates($payload, $thread)['elements'];
        $options = [];
        foreach ($payload as $index => $element) {
            if (($element['type'] ?? null) !== 'message') {
                continue;
            }
            $options[$index] = [];
            if ($states[$index]['active'] && $states[$index]['story_at'] !== null) {
                $options[$index][] = 'transfer';
            }
            if (! $states[$index]['active']) {
                $options[$index][] = 'poll';
            }
            $candidate = $this->sceneInsertion($payload, $index, $thread);
            if ($candidate !== null) {
                $options[$index][] = $candidate[$index]['scene_action'] === 'end' ? 'scene_end' : 'scene_start';
            }
        }

        return $options;
    }

    private function sceneInsertion(array $payload, int $position, ?Thread $thread): ?array
    {
        $state = $this->sceneStates($payload, $thread)['positions'][$position];
        foreach ($state['active'] ? ['end', 'start'] : ['start'] as $action) {
            $scene = [...$this->command([], 'add_scene_'.$action)[0], 'scene_action' => $action];
            if ($action === 'start') {
                $scene['scene_key'] ??= (string) Str::uuid();
            }
            $candidate = $payload;
            array_splice($candidate, $position, 0, [$scene]);
            if ($action === 'start') {
                for ($next = $position + 1; $next < count($candidate); $next++) {
                    if (($candidate[$next]['type'] ?? null) === 'scene_transition') {
                        break;
                    }
                    if (($candidate[$next]['type'] ?? null) === 'transfer') {
                        $candidate[$next]['scene_key'] = $scene['scene_key'];
                    }
                }
            }
            try {
                return $this->bindAndValidateScenes($candidate, $thread);
            } catch (ValidationException) {
                // Try a scene change when ending would strand a following transfer.
            }
        }

        return null;
    }

    private function timestamp(?string $value): ?int
    {
        return filled($value) ? CarbonImmutable::createFromFormat('Y-m-d\TH:i', $value, config('app.timezone'))->timestamp : null;
    }

    private function displayTimestamp(mixed $value): ?int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }
        if (! is_string($value)) {
            return null;
        }
        try {
            return $this->timestamp($value);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    private function characterAttributes(array $data): array
    {
        $createsCharacter = ($data['character'] ?? null) === 'new';

        return [
            'character_id' => $createsCharacter || ! is_numeric($data['character'] ?? null) ? null : (int) $data['character'],
            'creates_character' => $createsCharacter,
            'new_character_name' => $createsCharacter && filled($data['newcharname'] ?? null)
                ? trim((string) $data['newcharname'])
                : null,
        ];
    }
}
