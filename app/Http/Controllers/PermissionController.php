<?php

namespace App\Http\Controllers;

use App\Models\Board\Board;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Permit;
use App\Models\User;
use App\Support\LegacyTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function createForBoard(Request $request, Board $board): View|RedirectResponse
    {
        $this->authorize('view', $board);
        abort_unless($this->permissionService->allows('createpermission', $board, $request->user()), 403);

        if ($request->isMethod('post')) {
            $data = $this->validatedPermissionData($request);

            Permission::updateOrCreate([
                'table__recipient' => (int) $data['table__recipient'],
                'recipient' => (int) $data['recipient'],
                'table__subject' => LegacyTable::BOARD,
                'subject' => $board->id,
                'permit' => (int) $data['permit'],
            ], [
                'value' => (int) $data['value'],
            ]);

            Cache::flush();

            return redirect()->route('board.permissions', ['board' => $board->id]);
        }

        return view('permission.create-board', [
            'board' => $board,
            'permits' => Permit::query()->orderBy('name')->get(),
            'recipientTypes' => [
                LegacyTable::USER => 'Global/Nutzer',
                LegacyTable::GROUP => 'Gruppe',
            ],
        ]);
    }

    public function edit(Request $request, Permission $permission): View|RedirectResponse
    {
        $this->authorizeManage($permission);

        if ($request->isMethod('post')) {
            $data = $this->validatedPermissionData($request);

            $permission->update([
                'table__recipient' => (int) $data['table__recipient'],
                'recipient' => (int) $data['recipient'],
                'permit' => (int) $data['permit'],
                'value' => (int) $data['value'],
            ]);

            Cache::flush();

            return $this->redirectToSubject($permission);
        }

        return view('permission.edit', [
            'permission' => $permission->load(['permit_legacy', 'recipient_legacy', 'subject_legacy']),
            'permits' => Permit::query()->orderBy('name')->get(),
            'recipientTypes' => $this->recipientTypes(),
        ]);
    }

    public function delete(Request $request, Permission $permission): View|RedirectResponse
    {
        $this->authorizeManage($permission);

        if ($request->isMethod('post')) {
            $request->validate(['delete' => ['required', 'accepted']]);

            $redirect = $this->redirectToSubject($permission);
            $permission->delete();
            Cache::flush();

            return $redirect;
        }

        return view('permission.delete', [
            'permission' => $permission->load(['permit_legacy', 'recipient_legacy', 'subject_legacy']),
        ]);
    }

    private function validatedPermissionData(Request $request): array
    {
        $data = $request->validate([
            'table__recipient' => ['required', 'integer', 'in:0,4'],
            'recipient' => ['required', 'integer', 'min:0'],
            'permit' => ['required', 'integer', 'exists:dra_permit,id'],
            'value' => ['required', 'integer', 'in:0,1,2'],
        ]);

        if ((int) $data['table__recipient'] === LegacyTable::GROUP) {
            Group::findOrFail((int) $data['recipient']);
        }

        if ((int) $data['table__recipient'] === LegacyTable::USER && (int) $data['recipient'] !== 0) {
            User::findOrFail((int) $data['recipient']);
        }

        return $data;
    }

    private function authorizeManage(Permission $permission): void
    {
        $subject = $permission->subject_legacy;

        abort_unless($subject && $this->permissionService->allows('createpermission', $subject, request()->user()), 403);
    }

    private function redirectToSubject(Permission $permission): RedirectResponse
    {
        if ((int) $permission->table__subject === LegacyTable::BOARD) {
            return redirect()->route('board.permissions', ['board' => $permission->subject]);
        }

        return redirect()->route('board');
    }

    private function recipientTypes(): array
    {
        return [
            LegacyTable::USER => 'Global/Nutzer',
            LegacyTable::GROUP => 'Gruppe',
        ];
    }
}
