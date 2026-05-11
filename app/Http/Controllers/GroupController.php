<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class GroupController extends Controller
{
    private const PAGE_ENTRIES = 20;

    public function index(): RedirectResponse
    {
        return redirect()->route('user');
    }

    public function view(Group $group, int|string $page = 1): View
    {
        $this->setLocation($group);

        $users = $group->users()
            ->with('characters')
            ->select('dra_user.*')
            ->orderByRaw('LOWER(dra_user.name)')
            ->get();

        return view('group.view', [
            'group' => $group,
            'users' => $this->paginateCollection($users, (int) $page, url('/group/view/' . $group->id)),
        ]);
    }

    private function paginateCollection($items, int $page, string $baseUrl): LengthAwarePaginator
    {
        $page = max(1, $page);

        return new LengthAwarePaginator(
            $items->forPage($page, self::PAGE_ENTRIES)->values(),
            $items->count(),
            self::PAGE_ENTRIES,
            $page,
            ['path' => $baseUrl]
        );
    }
}
