<?php

namespace App\Http\Controllers;

use App\Data\Board\PostCompositionData;
use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\PostDraft;
use App\Models\Board\Thread as ForumThread;
use App\Services\Board\PostComposerViewData;
use App\Services\Board\PostDraftService;
use App\Services\Board\PostWriter;
use App\Services\Board\ThreadWriter;
use App\Services\PermissionService;
use App\Support\PostElementType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            if (! $this->drafts->isMeaningful($draft->payload) && blank($draft->title)) {
                $draft->delete();
            }
        });

        return view('draft.index', [
            'drafts' => $drafts->filter->exists,
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
            'character' => ['required', 'integer', 'exists:characters,id'],
            'elements' => ['array'],
            'elements.*' => ['array'],
            'elements.*.type' => ['required', Rule::enum(PostElementType::class)],
            'elements.*.message' => ['nullable', 'string'],
            'elements.*.smilies' => ['nullable', 'boolean'],
            'elements.*.scene_action' => ['nullable', Rule::in(['start', 'end'])],
            'elements.*.scene_key' => ['nullable', 'string', 'max:64'],
            'elements.*.location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'elements.*.story_at' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'elements.*.question' => ['nullable', 'string', 'max:255'],
            'elements.*.options' => ['nullable', 'array'],
            'elements.*.options.*' => ['nullable', 'string', 'max:255'],
            'elements.*.visibility' => ['nullable', Rule::in(['anonymous', 'open'])],
            'elements.*.max_choices' => ['nullable', 'integer', 'min:1'],
            'elements.*.closes_at' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'elements.*.transfer_action' => ['nullable', Rule::in(['give', 'drop', 'pickup', 'company_deposit', 'company_withdrawal'])],
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
        ]);
        abort_unless($request->user()->characters()->whereKey($data['character'])->exists(), 403);

        $intent = (string) ($data['intent'] ?? 'save');
        $elementIndex = $request->integer('element_index');
        $targetIndex = $request->integer('target_index');
        $optionIndex = $request->integer('option_index');
        if (preg_match('/^(move|remove|add_poll_option|remove_poll_option):(\d+)(?::(\d+))?$/', $intent, $command)) {
            $intent = $command[1];
            $elementIndex = (int) $command[2];
            if ($intent === 'move') {
                $targetIndex = (int) ($command[3] ?? 0);
            } elseif ($intent === 'remove_poll_option') {
                $optionIndex = (int) ($command[3] ?? 0);
            }
        }
        $payload = $this->drafts->normalizePayload((array) $request->input('elements', []));
        if ($intent !== 'save' && $intent !== 'autosave' && $intent !== 'publish') {
            $payload = $this->drafts->command(
                $payload,
                $intent,
                $elementIndex,
                $targetIndex,
                $optionIndex,
            );
        }
        $payload = $this->drafts->bindAndValidateScenes($payload, $draft->thread);
        $data['elements'] = $payload;
        if ($intent !== 'publish' && ! $this->drafts->isMeaningful($payload) && blank($data['title'] ?? null)) {
            $deleted = PostDraft::query()->whereKey($draft->id)->where('version', (int) $data['version'])->delete();
            if (! $deleted) {
                throw ValidationException::withMessages([
                    'draft' => 'Dieser Entwurf wurde inzwischen an anderer Stelle gespeichert. Die neuere Fassung wurde nicht überschrieben.',
                ]);
            }
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

        if ($intent === 'publish') {
            try {
                $composition = PostCompositionData::fromArray($draft->payload);
            } catch (InvalidArgumentException $exception) {
                throw ValidationException::withMessages(['elements' => $exception->getMessage()]);
            }
            $board = Board::findOrFail($draft->board_id);
            $this->authorize('createThread', $board);
            $thread = DB::transaction(function () use ($board, $request, $draft, $composition) {
                $thread = $this->threads->createComposition($board, $request->user(), $draft, $composition, $request->ip());
                $draft->delete();

                return $thread;
            });

            return redirect()->route('thread.view', $thread);
        }

        if ($request->expectsJson()) {
            return response()->json(['version' => $draft->version, 'saved_at' => $draft->updated_at?->toIso8601String()]);
        }

        return back()->with('status', 'Entwurf gespeichert.');
    }

    public function updateReply(Request $request, ForumThread $thread): RedirectResponse|JsonResponse
    {
        $this->authorize('create', [Post::class, $thread]);
        $data = $this->validateCompositionRequest($request, 0);
        abort_unless($request->user()->characters()->whereKey($data['character'])->exists(), 403);

        $intent = (string) ($data['intent'] ?? 'save');
        $elementIndex = $request->integer('element_index');
        $targetIndex = $request->integer('target_index');
        $optionIndex = $request->integer('option_index');
        $payload = $this->drafts->normalizePayload((array) $request->input('elements', []));

        if (preg_match('/^quote:(\d+)$/', $intent, $quoteCommand)) {
            $quotedPost = $thread->posts()->with(['character', 'author', 'elements.message'])->findOrFail((int) $quoteCommand[1]);
            $payload = $this->drafts->appendQuote($payload, $this->quoteText($quotedPost));
            $intent = 'quote';
        } else {
            if (preg_match('/^(move|remove|add_poll_option|remove_poll_option):(\d+)(?::(\d+))?$/', $intent, $command)) {
                $intent = $command[1];
                $elementIndex = (int) $command[2];
                if ($intent === 'move') {
                    $targetIndex = (int) ($command[3] ?? 0);
                } elseif ($intent === 'remove_poll_option') {
                    $optionIndex = (int) ($command[3] ?? 0);
                }
            }
            if (! in_array($intent, ['save', 'autosave', 'publish'], true)) {
                $payload = $this->drafts->command($payload, $intent, $elementIndex, $targetIndex, $optionIndex);
            }
        }

        try {
            $data['elements'] = $this->drafts->bindAndValidateScenes($payload, $thread);
        } catch (ValidationException $exception) {
            $data['elements'] = $payload;
            $this->drafts->saveReply($thread, $request->user(), $data, (int) $data['version']);

            throw $exception;
        }
        $draft = $this->drafts->saveReply($thread, $request->user(), $data, (int) $data['version']);

        if ($intent === 'publish') {
            if (! $draft->exists) {
                throw ValidationException::withMessages(['elements' => 'Der Beitrag enthält noch keinen Inhalt.']);
            }
            try {
                $composition = PostCompositionData::fromArray($draft->payload);
            } catch (InvalidArgumentException $exception) {
                throw ValidationException::withMessages(['elements' => $exception->getMessage()]);
            }
            $post = DB::transaction(function () use ($thread, $request, $draft, $composition) {
                $post = $this->posts->createComposition($thread, $request->user(), $draft->character_id, $composition, $request->ip());
                $draft->delete();

                return $post;
            });

            return redirect()->route('post.view', $post);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'version' => $draft->version,
                'saved_at' => $draft->exists ? $draft->updated_at?->toIso8601String() : null,
            ]);
        }

        $redirect = redirect()->route('thread.view', $thread)->withFragment('post-composer');
        if (! $draft->exists && ! in_array($intent, ['save', 'quote'], true)) {
            $redirect->withInput($request->except(['_token', 'intent']) + ['elements' => $data['elements']]);
        }

        return $redirect->with('status', $draft->exists ? 'Entwurf gespeichert.' : 'Ein leerer Entwurf wird nicht gespeichert.');
    }

    public function updateTopic(Request $request, ?Board $board = null): RedirectResponse|JsonResponse
    {
        $this->authorize('create', ForumThread::class);
        $data = $this->validateCompositionRequest($request, 0);
        abort_unless($request->user()->characters()->whereKey($data['character'])->exists(), 403);
        $selectedBoard = filled($data['board'] ?? null) ? Board::findOrFail((int) $data['board']) : $board;
        if ($selectedBoard) {
            $this->authorize('createThread', $selectedBoard);
            $data['board'] = $selectedBoard->id;
        }

        $intent = (string) ($data['intent'] ?? 'save');
        $elementIndex = $request->integer('element_index');
        $targetIndex = $request->integer('target_index');
        $optionIndex = $request->integer('option_index');
        if (preg_match('/^(move|remove|add_poll_option|remove_poll_option):(\d+)(?::(\d+))?$/', $intent, $command)) {
            $intent = $command[1];
            $elementIndex = (int) $command[2];
            if ($intent === 'move') {
                $targetIndex = (int) ($command[3] ?? 0);
            } elseif ($intent === 'remove_poll_option') {
                $optionIndex = (int) ($command[3] ?? 0);
            }
        }
        $payload = $this->drafts->normalizePayload((array) $request->input('elements', []));
        if (! in_array($intent, ['save', 'autosave', 'publish'], true)) {
            $payload = $this->drafts->command($payload, $intent, $elementIndex, $targetIndex, $optionIndex);
        }
        $data['elements'] = $this->drafts->bindAndValidateScenes($payload, null);
        $draft = $this->drafts->saveTopic($request->user(), $data, (int) $data['version']);

        if ($intent === 'publish') {
            if (! $draft->exists) {
                throw ValidationException::withMessages(['elements' => 'Das Thema enthält noch keinen Inhalt.']);
            }
            $board = Board::findOrFail($draft->board_id);
            try {
                $composition = PostCompositionData::fromArray($draft->payload);
            } catch (InvalidArgumentException $exception) {
                throw ValidationException::withMessages(['elements' => $exception->getMessage()]);
            }
            $thread = DB::transaction(function () use ($board, $request, $draft, $composition) {
                $thread = $this->threads->createComposition($board, $request->user(), $draft, $composition, $request->ip());
                $draft->delete();

                return $thread;
            });

            return redirect()->route('thread.view', $thread);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'action' => $draft->exists ? route('draft.update', $draft) : route('draft.topic.update', $board ?: []),
                'version' => $draft->version,
                'saved_at' => $draft->exists ? $draft->updated_at?->toIso8601String() : null,
            ]);
        }

        if ($draft->exists) {
            return redirect()->route('draft.edit', $draft)->with('status', 'Entwurf gespeichert.');
        }

        return redirect()->route('draft.topic', $board ?: [])->withInput($request->except(['_token', 'intent']) + ['elements' => $data['elements']])
            ->with('status', 'Ein leerer Entwurf wird nicht gespeichert.');
    }

    public function destroy(Request $request, PostDraft $draft): RedirectResponse
    {
        abort_unless($draft->user_id === $request->user()->id, 403);
        $draft->delete();

        return redirect()->route('draft.index')->with('status', 'Entwurf gelöscht.');
    }

    private function validateCompositionRequest(Request $request, int $minimumVersion): array
    {
        return $request->validate([
            'version' => ['required', 'integer', 'min:'.$minimumVersion],
            'board' => ['nullable', 'integer', 'exists:boards,id'],
            'title' => ['nullable', 'string', 'max:225'],
            'character' => ['required', 'integer', 'exists:characters,id'],
            'elements' => ['array'],
            'elements.*' => ['array'],
            'elements.*.type' => ['required', Rule::enum(PostElementType::class)],
            'elements.*.message' => ['nullable', 'string'],
            'elements.*.smilies' => ['nullable', 'boolean'],
            'elements.*.scene_action' => ['nullable', Rule::in(['start', 'end'])],
            'elements.*.scene_key' => ['nullable', 'string', 'max:64'],
            'elements.*.location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'elements.*.story_at' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'elements.*.question' => ['nullable', 'string', 'max:255'],
            'elements.*.options' => ['nullable', 'array'],
            'elements.*.options.*' => ['nullable', 'string', 'max:255'],
            'elements.*.visibility' => ['nullable', Rule::in(['anonymous', 'open'])],
            'elements.*.max_choices' => ['nullable', 'integer', 'min:1'],
            'elements.*.closes_at' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'elements.*.transfer_action' => ['nullable', Rule::in(['give', 'drop', 'pickup', 'company_deposit', 'company_withdrawal'])],
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
        ]);
    }

    private function quoteText(Post $post): string
    {
        $author = $post->character?->name ?? $post->author?->name ?? 'Unbekannter Charakter';

        return '[q='.str_replace(']', ')', $author).']'.trim($post->messageText()).'[/q]'.PHP_EOL;
    }
}
