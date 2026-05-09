<?php

namespace App\Http\Controllers;

use App\Models\Dictionary\Key;
use App\Models\Dictionary\Language;
use App\Models\Dictionary\Word;
use App\Models\Dictionary\WordType;
use App\Services\PermissionService;
use App\Services\Dictionary\DictionarySearchService;
use App\Services\Dictionary\DictionaryWriter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DictionaryController extends Controller
{
    public function __construct(
        PermissionService $permissionService,
        private DictionarySearchService $searchService,
        private DictionaryWriter $writer,
    ) {
        parent::__construct($permissionService);
    }

    public function index(): View
    {
        return $this->viewAll();
    }

    public function viewAll(int $languageFrom = 2, int $languageTo = 1, string $order = 'word,a'): View
    {
        $languageFrom = Language::findOrFail($languageFrom);
        $languageTo = Language::findOrFail($languageTo);
        [$orderColumn, $orderDirection] = $this->parseOrder($order);

        $words = Word::with(['wordType', 'translationKeysFrom.toWord.wordType'])
            ->where('language', $languageFrom->id)
            ->orderByRaw($orderColumn . ' ' . $orderDirection)
            ->get();

        return view('dictionary.index', [
            'canCreate' => $this->permissionService->check('createdictionary') > 0,
            'languageFrom' => $languageFrom,
            'languageTo' => $languageTo,
            'words' => $words,
            'wordTypes' => WordType::orderBy('id')->get(),
        ]);
    }

    public function view(Word $word): View
    {
        $word->load(['wordType', 'translationKeysFrom.toWord.wordType']);
        $this->setLocation($word);

        return view('dictionary.view', [
            'canCreate' => $this->permissionService->check('createdictionary') > 0,
            'canDelete' => $this->permissionService->check('deletedictionary') > 0,
            'canEdit' => $this->permissionService->check('editdictionary') > 0,
            'word' => $word,
        ]);
    }

    public function create(Request $request): RedirectResponse|View
    {
        if ($this->permissionService->check('createdictionary') === 0) {
            abort(403);
        }

        if ($request->isMethod('post')) {
            if ($request->filled('advanced-language-from') && $request->filled('advanced')) {
                $validated = $request->validate([
                    'advanced-language-from' => 'required|exists:dra_language,id',
                    'advanced-language-to' => 'required|exists:dra_language,id',
                    'advanced' => 'required|string',
                ]);

                $this->writer->createAdvancedWords($validated);

                return to_route('dictionary.index');
            }

            $validated = $request->validate([
                'language' => 'required|exists:dra_language,id',
                'wordtype' => 'required|exists:dra_wordtype,id',
                'word' => 'required|string',
            ]);

            $word = $this->writer->createWord($validated['language'], $validated['wordtype'], $validated['word']);

            return to_route('dictionary.view', ['word' => $word->id]);
        }

        return view('dictionary.create', $this->formOptions());
    }

    public function edit(Word $word, Request $request): RedirectResponse|View
    {
        $this->setLocation($word);

        if ($this->permissionService->check('editdictionary') === 0) {
            abort(403);
        }

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'language' => 'required|exists:dra_language,id',
                'wordtype' => 'required|exists:dra_wordtype,id',
                'word' => 'required|string',
            ]);

            $word->update([
                'language' => $validated['language'],
                'wordtype' => $validated['wordtype'],
                'word' => trim($validated['word']),
            ]);

            return to_route('dictionary.view', ['word' => $word->id]);
        }

        return view('dictionary.edit', $this->formOptions(['word' => $word]));
    }

    public function delete(Word $word, Request $request): RedirectResponse|View
    {
        $this->setLocation($word);

        if ($this->permissionService->check('deletedictionary') === 0) {
            abort(403);
        }

        $word->load(['translationKeysFrom.toWord', 'translationKeysTo.fromWord']);

        if ($request->isMethod('post')) {
            $this->writer->deleteWordWithKeys($word);

            return to_route('dictionary.index');
        }

        return view('dictionary.delete', ['word' => $word]);
    }

    public function createKey(Word $word, Request $request): RedirectResponse|View
    {
        $this->setLocation($word);

        if ($this->permissionService->check('createdictionary') === 0) {
            abort(403);
        }

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'word' => 'required|string',
                'bijective' => 'nullable|boolean',
            ]);

            $targetIds = collect(explode(',', $validated['word']))
                ->map(fn ($id) => (int)trim($id))
                ->filter(fn ($id) => $id > 0 && $id !== $word->id)
                ->unique();

            $this->writer->createTranslationKeys($word, $targetIds, $request->boolean('bijective'));

            return to_route('dictionary.view', ['word' => $word->id]);
        }

        $query = trim((string)$request->query('q', ''));
        $searchResults = $query !== '' ? $this->searchService->search($query, $word->id) : collect();

        if ($request->header('X-Dictionary-Search') === 'partial') {
            return view('dictionary._key-search-results', [
                'query' => $query,
                'searchResults' => $searchResults,
                'word' => $word,
            ]);
        }

        return view('dictionary.create-key', [
            'query' => $query,
            'searchResults' => $searchResults,
            'word' => $word,
        ]);
    }

    public function deleteKey(Key $key, Request $request): RedirectResponse|View
    {
        if ($this->permissionService->check('deletedictionary') === 0) {
            abort(403);
        }

        $key->load(['fromWord', 'toWord']);

        if ($request->isMethod('post')) {
            $wordId = $key->dictionary__from;
            $key->delete();

            return to_route('dictionary.view', ['word' => $wordId]);
        }

        return view('dictionary.delete-key', ['key' => $key]);
    }

    public function ajaxGetWords(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $exclude = $request->integer('exclude');
        $words = $this->searchService->search($query, $exclude ?: null);

        return response()->json($this->searchService->toAutocompletePayload($words));
    }

    private function formOptions(array $data = []): array
    {
        return array_merge([
            'languages' => Language::orderBy('id')->get(),
            'wordTypes' => WordType::orderBy('id')->get(),
        ], $data);
    }

    private function parseOrder(string $order): array
    {
        [$column, $direction] = array_pad(explode(',', $order, 2), 2, null);

        return match ($column) {
            'word' => ['LOWER(word)', $direction === 'd' ? 'DESC' : 'ASC'],
            default => ['LOWER(word)', 'ASC'],
        };
    }
}
