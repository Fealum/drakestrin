<?php

namespace App\Http\Controllers;

use App\Models\Board\Board;
use App\Models\Board\Thread as ForumThread;
use App\Models\Character;
use App\Models\Configuration;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BoardController extends Controller
{
    private const PAGE_ENTRIES = 20;

    public function index(): View
    {
        return $this->filter();
    }

    public function filterBoard(Board $board, int|string $page = 1): View
    {
        $this->authorize('view', $board);
        $this->setLocation($board);

        return $this->filter('board:' . $board->id, $page);
    }

    public function filter(?string $filter = null, int|string $page = 1): View
    {
        if (is_numeric($filter) && (int) $filter > 0) {
            $page = (int) $filter;
            $filter = null;
        }

        $filters = $this->parseFilterString($filter);
        $query = ForumThread::query()
            ->with(['boardModel', 'firstPost.characterModel', 'lastPost.characterModel'])
            ->orderByDesc('post__last_time');

        $this->applyFilters($query, $filters);

        $threads = $this->paginateCollection(
            $query->get()->filter(fn (ForumThread $thread) => Gate::allows('view', $thread))->values(),
            (int) $page,
            route('board.filter', ['filter' => $filter ?: null])
        );

        $boards = Board::with('childBoards.childBoards')
            ->where('board', 0)
            ->orderBy('sort')
            ->orderByRaw('LOWER(name)')
            ->get();
        $boards = $this->visibleBoardTree($boards);
        $viewedThreads = $this->viewedThreads();

        return view('board.filter', [
            'boards' => $boards,
            'canCreateThread' => auth()->check() && auth()->user()->can('create', ForumThread::class),
            'filter' => $filter,
            'filters' => $filters,
            'filterLookups' => $this->filterLookups($filters),
            'showSettings' => $this->boardShowSettings(),
            'threads' => $threads,
            'viewedBoards' => $this->viewedBoardIds($boards, $viewedThreads),
            'viewedThreads' => $viewedThreads,
        ]);
    }

    public function changeShow(Board $board, ?int $change = null, int $ajax = 0): Response|RedirectResponse
    {
        if (! auth()->check()) {
            return redirect()->route('board');
        }

        $current = $this->boardShowSettings()->get($board->id, 1);
        $change ??= $current === 0 ? 1 : 0;
        $change = $change === 0 ? 0 : 1;

        Configuration::query()->updateOrCreate([
            'table__recipient' => 0,
            'recipient' => auth()->id(),
            'table__subject' => 3,
            'subject' => $board->id,
            'setting' => 4,
        ], [
            'value' => $change,
        ]);

        if ($ajax === 1) {
            return response((string) $change);
        }

        return redirect()->back(fallback: route('board'));
    }

    public function permissions(Request $request, Board $board): View
    {
        $this->authorize('view', $board);

        $board->load([
            'parentBoard',
            'permissionsAsSubject.permit_legacy',
            'permissionsAsSubject.recipient_legacy',
        ]);

        $permissionNames = [
            'show' => 'Forum sehen',
            'createboard' => 'Unterforen erstellen',
            'editboard' => 'Unterforen bearbeiten',
            'deleteboard' => 'Unterforen löschen',
            'createthread' => 'Themen erstellen',
            'editthread' => 'Themen bearbeiten',
            'deletethread' => 'Themen löschen',
            'createpost' => 'Beiträge erstellen',
            'editpost' => 'Beiträge bearbeiten',
            'deletepost' => 'Beiträge löschen',
        ];

        return view('board.permissions', [
            'board' => $board,
            'canCreatePermission' => auth()->check() && $this->permissionService->allows('createpermission', $board, $request->user()),
            'effectivePermissions' => collect($permissionNames)
                ->map(fn (string $label, string $permit) => [
                    'permit' => $permit,
                    'label' => $label,
                    'value' => $this->permissionService->check($permit, $board, $request->user()),
                ])
                ->values(),
            'specificPermissions' => $board->permissionsAsSubject
                ->sortBy(fn (Permission $permission) => [
                    $permission->recipientName(),
                    $permission->permit_legacy?->name ?? '',
                ])
                ->values(),
        ]);
    }

    public function filterRedirect(Request $request): RedirectResponse
    {
        $filter = [];

        if ($request->filled('title')) {
            $filter[] = 'title:' . urlencode($request->string('title'));
        }

        if ($request->filled('message')) {
            $filter[] = 'message:' . urlencode($request->string('message'));
        }

        $boards = array_filter((array) $request->input('board', []));

        if ($boards) {
            $filter[] = 'board:' . implode(',', array_map('intval', $boards));
        }

        foreach (['user_first', 'user_contains', 'user_last', 'char_first', 'char_contains', 'char_last'] as $key) {
            $ids = $this->parseIds($request->string($key)->toString());

            if ($ids) {
                $filter[] = $key . ':' . implode(',', $ids);
            }
        }

        foreach (['date_first', 'date_last'] as $key) {
            if ($request->filled($key)) {
                $filter[] = $key . ':' . urlencode($request->string($key));
            }
        }

        return redirect($filter ? route('board.filter', ['filter' => implode(';', $filter)]) : route('board'));
    }

    public function ajaxGetUsers(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q'));

        return response()->json(
            User::query()
                ->when($query !== '', function (Builder $users) use ($query) {
                    $users->where('name', 'like', '%' . $query . '%');

                    if (ctype_digit($query)) {
                        $users->orWhereKey((int) $query);
                    }
                })
                ->orderByDesc('post__total')
                ->orderBy('name')
                ->limit(10)
                ->get()
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatarThumbUrl(),
                ])
        );
    }

    public function ajaxGetCharacters(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q'));

        return response()->json(
            Character::query()
                ->when($query !== '', function (Builder $characters) use ($query) {
                    $characters->where('name', 'like', '%' . $query . '%');

                    if (ctype_digit($query)) {
                        $characters->orWhereKey((int) $query);
                    }
                })
                ->orderByDesc('post__total')
                ->orderBy('name')
                ->limit(10)
                ->get()
                ->map(fn (Character $character) => [
                    'id' => $character->id,
                    'name' => $character->name,
                    'avatar' => $character->avatarThumbUrl(),
                ])
        );
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (($filters['title'] ?? '') !== '') {
            $query->where('name', 'like', '%' . $filters['title'] . '%');
        }

        if (($filters['message'] ?? '') !== '') {
            $query->whereHas('posts', fn (Builder $posts) => $posts->where('message', 'like', '%' . $filters['message'] . '%'));
        }

        if (($filters['board'] ?? []) !== []) {
            $query->whereIn('board', $filters['board']);
        }

        if (($filters['user_first'] ?? []) !== []) {
            $query->whereHas('firstPost', fn (Builder $post) => $post->whereIn('user', $filters['user_first']));
        }

        if (($filters['user_contains'] ?? []) !== []) {
            $query->whereHas('posts', fn (Builder $post) => $post->whereIn('user', $filters['user_contains']));
        }

        if (($filters['user_last'] ?? []) !== []) {
            $query->whereHas('lastPost', fn (Builder $post) => $post->whereIn('user', $filters['user_last']));
        }

        if (($filters['char_first'] ?? []) !== []) {
            $query->whereHas('firstPost', fn (Builder $post) => $post->whereIn('character', $filters['char_first']));
        }

        if (($filters['char_contains'] ?? []) !== []) {
            $query->whereHas('posts', fn (Builder $post) => $post->whereIn('character', $filters['char_contains']));
        }

        if (($filters['char_last'] ?? []) !== []) {
            $query->whereHas('lastPost', fn (Builder $post) => $post->whereIn('character', $filters['char_last']));
        }

        if (($filters['date_first'] ?? '') !== '') {
            [$from, $to] = $this->dateFilterRange($filters['date_first']);
            $this->applyDateRange($query, 'post__first_time', $from, $to);
        }

        if (($filters['date_last'] ?? '') !== '') {
            [$from, $to] = $this->dateFilterRange($filters['date_last']);
            $this->applyDateRange($query, 'post__last_time', $from, $to);
        }
    }

    private function parseFilterString(?string $filter): array
    {
        $filters = [
            'title' => null,
            'message' => null,
            'board' => [],
            'user_first' => [],
            'user_contains' => [],
            'user_last' => [],
            'char_first' => [],
            'char_contains' => [],
            'char_last' => [],
            'date_first' => null,
            'date_last' => null,
        ];

        if (! $filter) {
            return $filters;
        }

        foreach (explode(';', $filter) as $part) {
            [$key, $value] = array_pad(explode(':', $part, 2), 2, null);

            if ($key === 'title' || $key === 'message' || $key === 'date_first' || $key === 'date_last') {
                $filters[$key] = urldecode((string) $value);
            }

            if ($key === 'board') {
                $filters['board'] = $this->parseIds((string) $value);
            }

            if (in_array($key, ['user_first', 'user_contains', 'user_last', 'char_first', 'char_contains', 'char_last'], true)) {
                $filters[$key] = $this->parseIds((string) $value);
            }
        }

        return $filters;
    }

    private function parseIds(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn (string $id) => (int) trim($id))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function dateFilterRange(string $filter): array
    {
        if (str_ends_with($filter, 'd')) {
            return [Carbon::now()->subDays((int) substr($filter, 0, -1))->timestamp, null];
        }

        [$from, $to] = array_pad(explode('-', $filter, 2), 2, null);

        try {
            $from = $from ? Carbon::createFromFormat('d.m.Y', trim($from))->startOfDay()->timestamp : null;
            $to = $to ? Carbon::createFromFormat('d.m.Y', trim($to))->endOfDay()->timestamp : null;
        } catch (\Throwable) {
            return [null, null];
        }

        return [$from, $to];
    }

    private function applyDateRange(Builder $query, string $column, ?int $from, ?int $to): void
    {
        if ($from) {
            $query->where($column, '>=', $from);
        }

        if ($to) {
            $query->where($column, '<=', $to);
        }
    }

    private function filterLookups(array $filters): array
    {
        return [
            'users' => User::query()
                ->whereIn('id', array_unique(array_merge($filters['user_first'], $filters['user_contains'], $filters['user_last'])))
                ->get()
                ->keyBy('id')
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatarThumbUrl(),
                ]),
            'characters' => Character::query()
                ->whereIn('id', array_unique(array_merge($filters['char_first'], $filters['char_contains'], $filters['char_last'])))
                ->get()
                ->keyBy('id')
                ->map(fn (Character $character) => [
                    'id' => $character->id,
                    'name' => $character->name,
                    'avatar' => $character->avatarThumbUrl(),
                ]),
        ];
    }

    private function boardShowSettings()
    {
        if (! auth()->check()) {
            return collect();
        }

        return Configuration::query()
            ->where('table__recipient', 0)
            ->where('recipient', auth()->id())
            ->where('table__subject', 3)
            ->where('setting', 4)
            ->pluck('value', 'subject');
    }

    private function visibleBoardTree(Collection $boards): Collection
    {
        return $boards
            ->filter(fn (Board $board) => Gate::allows('view', $board))
            ->map(function (Board $board) {
                if ($board->relationLoaded('childBoards')) {
                    $board->setRelation('childBoards', $this->visibleBoardTree($board->childBoards));
                }

                return $board;
            })
            ->values();
    }

    private function viewedThreads(): array
    {
        return session('viewed.1', []);
    }

    private function viewedBoardIds(Collection $boards, array $viewedThreads): Collection
    {
        if (! auth()->check()) {
            return collect();
        }

        return $boards->flatMap(function (Board $board) use ($viewedThreads) {
            $ids = collect();
            $childIds = $this->viewedBoardIds($board->childBoards, $viewedThreads);

            if ($this->boardHasNewPosts($board, $viewedThreads) || $childIds->isNotEmpty()) {
                $ids->push($board->id);
            }

            return $ids->merge($childIds);
        })->unique()->values();
    }

    private function boardHasNewPosts(Board $board, array $viewedThreads): bool
    {
        $lastVisit = auth()->user()?->lastvisit?->timestamp ?? 0;

        return ForumThread::query()
            ->where('board', $board->id)
            ->where('post__last_time', '>=', $lastVisit)
            ->get()
            ->contains(fn (ForumThread $thread) => ($viewedThreads[$thread->id] ?? 0) < $thread->getRawOriginal('post__last_time'));
    }

    private function paginateCollection($items, int $page, string $path): LengthAwarePaginator
    {
        $page = max($page, 1);

        return new LengthAwarePaginator(
            $items->forPage($page, self::PAGE_ENTRIES)->values(),
            $items->count(),
            self::PAGE_ENTRIES,
            $page,
            ['path' => $path]
        );
    }
}
