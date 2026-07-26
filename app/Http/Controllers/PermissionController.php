<?php

namespace App\Http\Controllers;

use App\Models\Board\Board;
use App\Models\Access\Group;
use App\Models\Access\Permission;
use App\Models\Access\Permit;
use App\Models\User;
use App\Support\PermissionEntityType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function createForBoard(Request $request, Board $board): View|RedirectResponse
    {
        $this->authorize('view', $board);
        abort_unless($this->permissionService->allows('createpermission', $board, $request->user()), 403);

        if ($request->isMethod('post')) {
            $data = $this->validatedPermissionData($request);

            $recipientType = PermissionEntityType::fromDatabase($data['recipient_type']);

            Permission::updateOrCreate([
                'recipient_type' => $recipientType->value,
                'recipient_id' => (int) $data['recipient_id'],
                'subject_type' => PermissionEntityType::BOARD->value,
                'subject_id' => $board->id,
                'permit_id' => (int) $data['permit_id'],
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
                PermissionEntityType::USER->value => 'Global/Nutzer',
                PermissionEntityType::GROUP->value => 'Gruppe',
            ],
        ]);
    }

    public function edit(Request $request, Permission $permission): View|RedirectResponse
    {
        $this->authorizeManage($permission);

        if ($request->isMethod('post')) {
            $data = $this->validatedPermissionData($request);
            $recipientType = PermissionEntityType::fromDatabase($data['recipient_type']);

            $permission->update([
                'recipient_type' => $recipientType->value,
                'recipient_id' => (int) $data['recipient_id'],
                'permit_id' => (int) $data['permit_id'],
                'value' => (int) $data['value'],
            ]);

            Cache::flush();

            return $this->redirectToSubject($permission);
        }

        return view('permission.edit', [
            'permission' => $permission->load(['permit', 'recipient', 'subject']),
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
            'permission' => $permission->load(['permit', 'recipient', 'subject']),
        ]);
    }

    private function validatedPermissionData(Request $request): array
    {
        $data = $request->validate([
            'recipient_type' => ['required', Rule::in([
                PermissionEntityType::USER->value,
                PermissionEntityType::GROUP->value,
            ])],
            'recipient_id' => ['required', 'integer', 'min:0'],
            'permit_id' => ['required', 'integer', 'exists:permits,id'],
            'value' => ['required', 'integer', 'in:0,1,2'],
        ]);

        $recipientType = PermissionEntityType::fromDatabase($data['recipient_type']);

        if ($recipientType === PermissionEntityType::GROUP) {
            Group::findOrFail((int) $data['recipient_id']);
        }

        if ($recipientType === PermissionEntityType::USER && (int) $data['recipient_id'] !== 0) {
            User::findOrFail((int) $data['recipient_id']);
        }

        return $data;
    }

    private function authorizeManage(Permission $permission): void
    {
        $subject = $permission->subject;

        abort_unless($subject && $this->permissionService->allows('createpermission', $subject, request()->user()), 403);
    }

    private function redirectToSubject(Permission $permission): RedirectResponse
    {
        if (PermissionEntityType::fromDatabase($permission->subject_type) === PermissionEntityType::BOARD) {
            return redirect()->route('board.permissions', ['board' => $permission->subject_id]);
        }

        return redirect()->route('board');
    }

    private function recipientTypes(): array
    {
        return [
            PermissionEntityType::USER->value => 'Global/Nutzer',
            PermissionEntityType::GROUP->value => 'Gruppe',
        ];
    }
}
