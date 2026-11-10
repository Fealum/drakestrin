<?php

namespace App\Http\Controllers;

use App\Data\Board\PostCompositionData;
use App\Http\Requests\Board\SearchComposerLocationsRequest;
use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\PostDraft;
use App\Models\Board\Thread as ForumThread;
use App\Repositories\Territory\LocationRepository;
use App\Services\Board\PostComposerViewData;
use App\Services\Board\PostDraftService;
use App\Services\Board\PostWriter;
use App\Services\Board\ThreadWriter;
use App\Services\PermissionService;
use App\Support\PostElementType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

class PostDraftController extends Controller
{
    public function __construct(
        PermissionService $permissions,
        private PostDraftService $drafts,
        private PostComposerViewData $composer,
        private PostWriter $posts,
        private ThreadWriter $threads,
    ) {
        parent::__construct($permissions);
    }

    public function index(Request $request): View
    {
        $drafts = PostDraft::query()->with(['thread.board', 'board'])
            ->where('user_id', $request->user()->id)->latest('updated_at')->get();
        $drafts->each(function (PostDraft $draft) {
            if (! $this->drafts->isMeaningful($draft->payload) && blank($draft->title) && blank($draft->new_character_name)) {
                $draft->delete();
            }
        });

        return view('draft.index', [
            'drafts' => $drafts->filter->exists,
        ]);
    }

    public function locations(SearchComposerLocationsRequest $request, LocationRepository $locations): View
    {
        return view('draft._locations', [
            'index' => $request->integer('index'),
            'selected' => $request->integer('selected') ?: null,
            'choices' => $locations->composerChoices((string) $request->input('q', ''), $request->integer('page', 1), $request->integer('selected') ?: null),
        ]);
    }

    public function topic(Request $request, ?Board $board = null): View
    {
        $this->authorize('create', ForumThread::class);
        if ($board) {
            $this->authorize('createThread', $board);
        }

        $draft = $this->drafts->topic($request->user(), $board);

        return view('draft.page', $this->composer->make($draft, $request->user(), null));
    }

    public function edit(Request $request, PostDraft $draft): View
    {
        abort_unless($draft->user_id === $request->user()->id, 403);
        $draft->load(['thread.currentScene.location', 'board']);
        $thread = $draft->thread;
        abort_if($thread, 404);

        return view('draft.page', $this->composer->make($draft, $request->user(), null));
    }

    public function update(Request $request, PostDraft $draft): RedirectResponse|JsonResponse
    {
        abort_unless($request->user() && $draft->user_id === $request->user()->id, 403);
        abort_if($draft->thread_id, 404);
        $data = $request->validate([
            'version' => ['required', 'integer', 'min:1'],
            'board' => ['nullable', 'integer', 'exists:boards,id'],
            'title' => ['nullable', 'string', 'max:225'],
            'character' => ['required'],
            'newcharname' => ['nullable', 'string', 'max:85'],
            'elements' => ['array'],
            'elements.*' => ['array'],
            'elements.*.type' => ['required', Rule::enum(PostElementType::class)],
            'elements.*.message' => ['nullable', 'string'],
            'elements.*.editor_key' => ['nullable', 'uuid'],
            'elements.*.smilies' => ['nullable', 'boolean'],
            'elements.*.scene_action' => ['nullable', Rule::in(['start', 'end'])],
            'elements.*.scene_key' => ['nullable', 'string', 'max:64'],
            'elements.*.location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'elements.*.location_query' => ['nullable', 'string', 'max:100'],
            'elements.*.location_page' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'elements.*.story_at' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'elements.*.question' => ['nullable', 'string', 'max:255'],
            'elements.*.options' => ['nullable', 'array'],
            'elements.*.options.*' => ['nullable', 'string', 'max:255'],
            'elements.*.visibility' => ['nullable', Rule::in(['anonymous', 'open'])],
            'elements.*.max_choices' => ['nullable', 'integer', 'min:1'],
            'elements.*.closes_at' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'elements.*.transfer_action' => ['nullable', Rule::in(['give', 'drop', 'pickup', 'company_deposit', 'company_withdrawal'])],
            'elements.*.source' => ['nullable', 'string', 'max:64'],
            'elements.*.recipient_party' => ['nullable', 'string', 'max:64'],
            'elements.*.recipient' => ['nullable', 'integer', 'exists:characters,id'],
            'elements.*.company_site' => ['nullable', 'integer', 'exists:company_sites,id'],
            'elements.*.inventory' => ['nullable', 'array'],
            'elements.*.inventory.*' => ['integer', 'exists:inventories,id'],
            'elements.*.inventorystack' => ['nullable', 'array'],
            'elements.*.inventorystack.*' => ['nullable', 'string', 'max:32'],
            'intent' => ['nullable', 'string'],
            'element_index' => ['nullable', 'integer', 'min:0'],
            'option_index' => ['nullable', 'integer', 'min:0'],
            'target_index' => ['nullable', 'integer', 'min:0'],
            'move_targets' => ['nullable', 'array'],
            'move_targets.*' => ['integer', 'min:0'],
            'remove_elements' => ['nullable', 'array'],
            'remove_elements.*' => ['boolean'],
        ]);
        $authorTarget = new ForumThread(['board_id' => (int) ($data['board'] ?? $draft->board_id ?? 0)]);
        $this->validateCharacterSelection($request, $data, $authorTarget, ($data['intent'] ?? null) === 'publish');

        $intent = (string) ($data['intent'] ?? 'save');
        $elementIndex = $request->integer('element_index');
        $targetIndex = $request->integer('target_index');
        $optionIndex = $request->integer('option_index');
        if (preg_match('/^insert:(add_message|add_transfer|add_scene_start|add_scene_end|add_poll):(\d+)$/', $intent, $command)) {
            $intent = $command[1];
            $targetIndex = (int) $command[2];
        } elseif (preg_match('/^(move|move_selected|remove|add_poll_option|remove_poll_option|convert_markers):(\d+)(?::(\d+))?$/', $intent, $command)) {
            $intent = $command[1];
            $elementIndex = (int) $command[2];
            if ($intent === 'move') {
                $targetIndex = (int) ($command[3] ?? 0);
            } elseif ($intent === 'remove_poll_option') {
                $optionIndex = (int) ($command[3] ?? 0);
            }
        }
        $payload = $this->drafts->normalizePayload((array) $request->input('elements', []));
        $payload = $this->prepareSubmission($request, $data, $payload, null, $draft);
        if ($request->attributes->get('composer.markers_expanded')) {
            $intent = 'save';
        }
        if ($intent !== 'save' && $intent !== 'autosave' && $intent !== 'publish') {
            $payload = $this->applyDraftCommand($request, $data, $payload, $intent, $elementIndex, $targetIndex, $optionIndex, null, $draft);
        }
        $payload = $this->drafts->bindAndValidateScenes($payload, $draft->thread);
        $data['elements'] = $payload;
        if ($intent !== 'publish' && ! $this->drafts->isMeaningful($payload) && blank($data['title'] ?? null) && blank($data['newcharname'] ?? null)) {
            $deleted = PostDraft::query()->whereKey($draft->id)->where('version', (int) $data['version'])->delete();
            if (! $deleted) {
                throw ValidationException::withMessages([
                    'draft' => 'Dieser Entwurf wurde inzwischen an anderer Stelle gespeichert. Die neuere Fassung wurde nicht überschrieben.',
                ]);
            }
            $request->attributes->set('composer.draft', $this->drafts->saveTopic($request->user(), $data, 0));
            if ($request->expectsJson()) {
                return response()->json([
                    'action' => route('draft.topic.update', $draft->board ?: []),
                    'version' => 0,
                    'saved_at' => null,
                ]);
            }

            return redirect()->route('draft.topic', $draft->board ?: [])->with('status', 'Der leere Entwurf wurde gelöscht.');
        }
        $draft = $this->drafts->save($draft, $request->user(), $data, (int) $data['version']);
        $request->attributes->set('composer.draft', $draft);

        if ($intent === 'publish') {
            try {
                $this->drafts->assertMarkersConverted($draft->payload);
                $composition = PostCompositionData::fromArray($this->drafts->mergeAdjacentMessages($draft->payload));
            } catch (InvalidArgumentException $exception) {
                throw ValidationException::withMessages(['elements' => $exception->getMessage()]);
            }
            $board = Board::findOrFail($draft->board_id);
            $this->authorize('createThread', $board);
            $thread = $this->drafts->publish($draft, function () use ($board, $request, $draft, $composition) {
                $thread = $this->threads->createComposition($board, $request->user(), $draft, $composition, $request->ip());
                $draft->delete();

                return $thread;
            });

            return redirect()->route('thread.view', $thread);
        }

        if ($request->expectsJson()) {
            return response()->json(['version' => $draft->version, 'saved_at' => $draft->updated_at?->toIso8601String(), 'controls' => $this->composerControls($payload, null)]);
        }

        return back()->with('status', $this->draftStatus($request));
    }

    public function updateReply(Request $request, ForumThread $thread): RedirectResponse|JsonResponse
    {
        $this->authorize('create', [Post::class, $thread]);
        $data = $this->validateCompositionRequest($request, 0);
        $this->validateCharacterSelection($request, $data, $thread, ($data['intent'] ?? null) === 'publish');

        $intent = (string) ($data['intent'] ?? 'save');
        $elementIndex = $request->integer('element_index');
        $targetIndex = $request->integer('target_index');
        $optionIndex = $request->integer('option_index');
        $payload = $this->drafts->normalizePayload((array) $request->input('elements', []));
        $payload = $this->prepareSubmission($request, $data, $payload, $thread);
        if ($request->attributes->get('composer.markers_expanded')) {
            $intent = 'save';
        }

        if (preg_match('/^quote:(\d+)$/', $intent, $quoteCommand)) {
            $quotedPost = $thread->posts()->with(['character', 'author', 'elements.message'])->findOrFail((int) $quoteCommand[1]);
            $payload = $this->drafts->appendQuote($payload, $this->quoteText($quotedPost));
            $intent = 'quote';
        } else {
            if (preg_match('/^insert:(add_message|add_transfer|add_scene_start|add_scene_end|add_poll):(\d+)$/', $intent, $command)) {
                $intent = $command[1];
                $targetIndex = (int) $command[2];
            } elseif (preg_match('/^(move|move_selected|remove|add_poll_option|remove_poll_option|convert_markers):(\d+)(?::(\d+))?$/', $intent, $command)) {
                $intent = $command[1];
                $elementIndex = (int) $command[2];
                if ($intent === 'move') {
                    $targetIndex = (int) ($command[3] ?? 0);
                } elseif ($intent === 'remove_poll_option') {
                    $optionIndex = (int) ($command[3] ?? 0);
                }
            }
            if (! in_array($intent, ['save', 'autosave', 'publish'], true)) {
                $payload = $this->applyDraftCommand($request, $data, $payload, $intent, $elementIndex, $targetIndex, $optionIndex, $thread);
            }
        }

        try {
            $data['elements'] = $this->drafts->bindAndValidateScenes($payload, $thread);
        } catch (ValidationException $exception) {
            $data['elements'] = $payload;
            $savedDraft = $this->drafts->saveReply($thread, $request->user(), $data, (int) $data['version']);
            $request->attributes->set('composer.draft', $savedDraft);

            throw $exception;
        }
        $draft = $this->drafts->saveReply($thread, $request->user(), $data, (int) $data['version']);
        $request->attributes->set('composer.draft', $draft);
        $request->attributes->set('composer.thread', $thread);

        if ($intent === 'publish') {
            if (! $draft->exists) {
                throw ValidationException::withMessages(['elements' => 'Der Beitrag enthält noch keinen Inhalt.']);
            }
            try {
                $this->drafts->assertMarkersConverted($draft->payload);
                $composition = PostCompositionData::fromArray($this->drafts->mergeAdjacentMessages($draft->payload));
            } catch (InvalidArgumentException $exception) {
                throw ValidationException::withMessages(['elements' => $exception->getMessage()]);
            }
            $post = $this->drafts->publish($draft, function () use ($thread, $request, $draft, $composition) {
                $post = $this->posts->createComposition(
                    $thread,
                    $request->user(),
                    $draft->characterSelection(),
                    $composition,
                    $request->ip(),
                    $draft->new_character_name,
                );
                $draft->delete();

                return $post;
            });

            return redirect()->route('post.view', $post);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'version' => $draft->version,
                'saved_at' => $draft->exists ? $draft->updated_at?->toIso8601String() : null,
                'controls' => $this->composerControls($payload, $thread),
            ]);
        }

        $threadPage = max((int) ($data['thread_page'] ?? 1), 1);
        $redirect = redirect()->route('thread.view', [
            'thread' => $thread,
            'page' => $threadPage > 1 ? $threadPage : null,
        ])->withFragment('post-composer');
        if (! $draft->exists || ($intent === 'quote' && ($request->has('move_targets') || $request->has('remove_elements')))) {
            $redirect->withInput(array_replace($request->except(['_token', 'intent']), ['elements' => $data['elements']]));
        }

        return $redirect->with('status', $draft->exists ? $this->draftStatus($request) : 'Ein leerer Entwurf wird nicht gespeichert.');
    }

    public function updateTopic(Request $request, ?Board $board = null): RedirectResponse|JsonResponse
    {
        $this->authorize('create', ForumThread::class);
        $data = $this->validateCompositionRequest($request, 0);
        $selectedBoard = filled($data['board'] ?? null) ? Board::findOrFail((int) $data['board']) : $board;
        if ($selectedBoard) {
            $this->authorize('createThread', $selectedBoard);
            $data['board'] = $selectedBoard->id;
        }
        $this->validateCharacterSelection(
            $request,
            $data,
            new ForumThread(['board_id' => $selectedBoard?->id ?? 0]),
            ($data['intent'] ?? null) === 'publish',
        );

        $intent = (string) ($data['intent'] ?? 'save');
        $elementIndex = $request->integer('element_index');
        $targetIndex = $request->integer('target_index');
        $optionIndex = $request->integer('option_index');
        if (preg_match('/^insert:(add_message|add_transfer|add_scene_start|add_scene_end|add_poll):(\d+)$/', $intent, $command)) {
            $intent = $command[1];
            $targetIndex = (int) $command[2];
        } elseif (preg_match('/^(move|move_selected|remove|add_poll_option|remove_poll_option|convert_markers):(\d+)(?::(\d+))?$/', $intent, $command)) {
            $intent = $command[1];
            $elementIndex = (int) $command[2];
            if ($intent === 'move') {
                $targetIndex = (int) ($command[3] ?? 0);
            } elseif ($intent === 'remove_poll_option') {
                $optionIndex = (int) ($command[3] ?? 0);
            }
        }
        $payload = $this->drafts->normalizePayload((array) $request->input('elements', []));
        $payload = $this->prepareSubmission($request, $data, $payload, null);
        if ($request->attributes->get('composer.markers_expanded')) {
            $intent = 'save';
        }
        if (! in_array($intent, ['save', 'autosave', 'publish'], true)) {
            $payload = $this->applyDraftCommand($request, $data, $payload, $intent, $elementIndex, $targetIndex, $optionIndex, null);
        }
        $data['elements'] = $this->drafts->bindAndValidateScenes($payload, null);
        $draft = $this->drafts->saveTopic($request->user(), $data, (int) $data['version']);
        $request->attributes->set('composer.draft', $draft);

        if ($intent === 'publish') {
            if (! $draft->exists) {
                throw ValidationException::withMessages(['elements' => 'Das Thema enthält noch keinen Inhalt.']);
            }
            $board = Board::findOrFail($draft->board_id);
            try {
                $this->drafts->assertMarkersConverted($draft->payload);
                $composition = PostCompositionData::fromArray($this->drafts->mergeAdjacentMessages($draft->payload));
            } catch (InvalidArgumentException $exception) {
                throw ValidationException::withMessages(['elements' => $exception->getMessage()]);
            }
            $thread = $this->drafts->publish($draft, function () use ($board, $request, $draft, $composition) {
                $thread = $this->threads->createComposition($board, $request->user(), $draft, $composition, $request->ip());
                $draft->delete();

                return $thread;
            });

            return redirect()->route('thread.view', $thread);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'action' => $draft->exists ? route('draft.update', $draft) : route('draft.topic.update', $board ?: []),
                'resume_url' => $draft->exists ? route('draft.edit', $draft) : route('draft.topic', $board ?: []),
                'version' => $draft->version,
                'saved_at' => $draft->exists ? $draft->updated_at?->toIso8601String() : null,
                'controls' => $this->composerControls($payload, null),
            ]);
        }

        if ($draft->exists) {
            return redirect()->route('draft.edit', $draft)->with('status', $this->draftStatus($request));
        }

        return redirect()->route('draft.topic', $board ?: [])->withInput(array_replace($request->except(['_token', 'intent']), ['elements' => $data['elements']]))
            ->with('status', 'Ein leerer Entwurf wird nicht gespeichert.');
    }

    public function destroy(Request $request, PostDraft $draft): RedirectResponse
    {
        abort_unless($draft->user_id === $request->user()->id, 403);
        $draft->delete();

        return redirect()->route('draft.index')->with('status', 'Entwurf gelöscht.');
    }

    private function applyDraftCommand(
        Request $request,
        array $data,
        array $payload,
        string $intent,
        int $index,
        int $target,
        int $option,
        ?ForumThread $thread,
        ?PostDraft $draft = null,
    ): array {
        if (preg_match('/^location_search:(\d+):(\d+)$/', $intent, $search)) {
            $position = (int) $search[1];
            abort_unless(($payload[$position]['type'] ?? null) === 'scene_transition', 422);
            $payload[$position]['location_page'] = max(1, min(100000, (int) $search[2]));

            return $payload;
        }
        if ($intent === 'move_selected') {
            $target = $request->integer('move_targets.'.$index, -1);
            if (! in_array($target, $this->drafts->validMoveTargets($payload, $thread)[$index] ?? [], true)) {
                throw ValidationException::withMessages(['elements.'.$index => 'Diese Position ist für den Baustein nicht zulässig.']);
            }
            $intent = 'move';
        }
        if ($intent !== 'convert_markers') {
            return $this->drafts->command($payload, $intent, $index, $target, $option);
        }
        try {
            return $this->drafts->convertMarkers($payload, $request->input('intent') === 'convert_markers' ? null : $index, $request->user(), $thread, $data['board'] ?? $draft?->board_id);
        } catch (ValidationException $exception) {
            // Keep the original text, never a partially converted composition.
            $this->retainDraft($request, $data, $payload, $thread, $draft);

            throw $exception;
        }
    }

    private function prepareSubmission(Request $request, array $data, array $payload, ?ForumThread $thread, ?PostDraft $draft = null): array
    {
        if (! in_array($data['intent'] ?? 'save', ['save', 'publish'], true)) {
            return $payload;
        }
        try {
            $edited = $this->drafts->applyPendingEdits($payload, $data['move_targets'] ?? [], $data['remove_elements'] ?? []);
            $expanded = $this->drafts->containsMarkers($edited);
            $edited = $this->drafts->convertMarkers($edited, null, $request->user(), $thread, $data['board'] ?? $draft?->board_id);
        } catch (ValidationException $exception) {
            $this->retainDraft($request, $data, $payload, $thread, $draft);

            throw $exception;
        }
        if ($expanded) {
            $request->attributes->set('composer.markers_expanded', true);
            $keys = array_values(array_diff(array_filter(array_column($edited, 'editor_key')), array_column($payload, 'editor_key')));
            $request->attributes->set('composer.inserted_keys', $keys);
            if ($request->header('HX-Request') !== 'true') {
                $request->session()->flash('composer_inserted_keys', $keys);
            }
        }

        return $edited;
    }

    private function retainDraft(Request $request, array $data, array $payload, ?ForumThread $thread, ?PostDraft $draft): void
    {
        $data['elements'] = $payload;
        $version = (int) $data['version'];
        $saved = $thread
            ? $this->drafts->saveReply($thread, $request->user(), $data, $version)
            : ($draft
                ? $this->drafts->save($draft, $request->user(), $data, $version)
                : $this->drafts->saveTopic($request->user(), $data, $version));
        $request->attributes->set('composer.draft', $saved);
        $request->attributes->set('composer.thread', $thread);
    }

    private function draftStatus(Request $request): string
    {
        return $request->attributes->get('composer.markers_expanded') ? '' : 'Entwurf gespeichert.';
    }

    private function composerControls(array $payload, ?ForumThread $thread): array
    {
        return ['moves' => $this->drafts->validMoveTargets($payload, $thread), 'markers' => $this->drafts->markerOptions($payload, $thread)];
    }

    private function validateCompositionRequest(Request $request, int $minimumVersion): array
    {
        return $request->validate([
            'version' => ['required', 'integer', 'min:'.$minimumVersion],
            'board' => ['nullable', 'integer', 'exists:boards,id'],
            'title' => ['nullable', 'string', 'max:225'],
            'character' => ['required'],
            'newcharname' => ['nullable', 'string', 'max:85'],
            'elements' => ['array'],
            'elements.*' => ['array'],
            'elements.*.type' => ['required', Rule::enum(PostElementType::class)],
            'elements.*.message' => ['nullable', 'string'],
            'elements.*.editor_key' => ['nullable', 'uuid'],
            'elements.*.smilies' => ['nullable', 'boolean'],
            'elements.*.scene_action' => ['nullable', Rule::in(['start', 'end'])],
            'elements.*.scene_key' => ['nullable', 'string', 'max:64'],
            'elements.*.location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'elements.*.location_query' => ['nullable', 'string', 'max:100'],
            'elements.*.location_page' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'elements.*.story_at' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'elements.*.question' => ['nullable', 'string', 'max:255'],
            'elements.*.options' => ['nullable', 'array'],
            'elements.*.options.*' => ['nullable', 'string', 'max:255'],
            'elements.*.visibility' => ['nullable', Rule::in(['anonymous', 'open'])],
            'elements.*.max_choices' => ['nullable', 'integer', 'min:1'],
            'elements.*.closes_at' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'elements.*.transfer_action' => ['nullable', Rule::in(['give', 'drop', 'pickup', 'company_deposit', 'company_withdrawal'])],
            'elements.*.source' => ['nullable', 'string', 'max:64'],
            'elements.*.recipient_party' => ['nullable', 'string', 'max:64'],
            'elements.*.recipient' => ['nullable', 'integer', 'exists:characters,id'],
            'elements.*.company_site' => ['nullable', 'integer', 'exists:company_sites,id'],
            'elements.*.inventory' => ['nullable', 'array'],
            'elements.*.inventory.*' => ['integer', 'exists:inventories,id'],
            'elements.*.inventorystack' => ['nullable', 'array'],
            'elements.*.inventorystack.*' => ['nullable', 'string', 'max:32'],
            'intent' => ['nullable', 'string'],
            'element_index' => ['nullable', 'integer', 'min:0'],
            'option_index' => ['nullable', 'integer', 'min:0'],
            'target_index' => ['nullable', 'integer', 'min:0'],
            'thread_page' => ['nullable', 'integer', 'min:1'],
            'move_targets' => ['nullable', 'array'],
            'move_targets.*' => ['integer', 'min:0'],
            'remove_elements' => ['nullable', 'array'],
            'remove_elements.*' => ['boolean'],
        ]);
    }

    private function quoteText(Post $post): string
    {
        $author = $post->character?->name ?? $post->author?->name ?? 'Unbekannter Charakter';

        return '[q='.str_replace(']', ')', $author).']'.trim($post->messageText()).'[/q]'.PHP_EOL;
    }

    private function validateCharacterSelection(
        Request $request,
        array $data,
        ForumThread $target,
        bool $publishing,
    ): void {
        $selection = (string) ($data['character'] ?? '');

        if ($selection === 'new') {
            abort_unless($this->permissionService->allows('createcharacter', $target, $request->user()), 403);
            if ($publishing && blank($data['newcharname'] ?? null)) {
                throw ValidationException::withMessages(['newcharname' => 'Bitte gib einen Namen für den neuen Charakter ein.']);
            }

            return;
        }

        abort_unless(ctype_digit($selection) && $request->user()->characters()->whereKey((int) $selection)->exists(), 403);
    }
}
