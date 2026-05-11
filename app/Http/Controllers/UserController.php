<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Protocol;
use App\Models\User;
use App\Models\UserContact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Intervention\Image\ImageManager;
use Throwable;

class UserController extends Controller
{
    private const PAGE_ENTRIES = 20;

    public function index(): View
    {
        return $this->viewAll();
    }

    public function viewAll(?string $order = null, int|string $page = 1): View
    {
        if (is_numeric($order)) {
            $page = $order;
            $order = null;
        }

        $order ??= 'postsperday,d;name,a';
        $query = User::query()
            ->with('characters')
            ->select('dra_user.*');

        $this->applyOrder($query, $order);

        $users = $query->get();

        return view('user.viewall', [
            'order' => $order,
            'users' => $this->paginateCollection(
                $users,
                (int) $page,
                url('/user/viewall/' . $order)
            ),
        ]);
    }

    public function view(User $user): View
    {
        $user->load([
            'characters',
            'contacts.protocolModel',
            'groups',
        ]);

        $this->setLocation($user);

        $viewer = auth()->user();
        $canEdit = $this->canEditUser($user, $viewer);
        $canCreateCharacter = $viewer && $this->permissionService->allowsOwn('createcharacter', $user, $user->id, $viewer);

        return view('user.view', [
            'canCreateCharacter' => $canCreateCharacter,
            'canEdit' => $canEdit,
            'user' => $user,
        ]);
    }

    public function edit(Request $request, User $user): View|RedirectResponse
    {
        $user->load('characters');
        $this->setLocation($user);
        abort_unless($this->canEditUser($user, $request->user()), 403);

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'character__avatar' => ['nullable', 'integer', 'exists:dra_character,id'],
                'usertext' => ['nullable', 'string'],
                'gender' => ['nullable', 'integer', 'in:0,1,2,3'],
                'birthday' => ['nullable', 'integer', 'min:0'],
                'location' => ['nullable', 'string', 'max:255'],
                'interests' => ['nullable', 'string', 'max:255'],
                'work' => ['nullable', 'string', 'max:255'],
            ]);

            $avatarId = (int) ($data['character__avatar'] ?? 0);

            if ($avatarId && ! $user->characters->contains('id', $avatarId)) {
                abort_unless($this->permissionService->allows('edituser', $user, $request->user()), 403);
            }

            $user->update([
                'character__avatar' => $avatarId ?: null,
                'usertext' => $data['usertext'] ?? '',
                'gender' => (int) ($data['gender'] ?? 0),
                'birthday' => (int) ($data['birthday'] ?? 0),
                'location' => $data['location'] ?? '',
                'interests' => $data['interests'] ?? '',
                'work' => $data['work'] ?? '',
            ]);

            return redirect()->route('user.view', ['user' => $user->id]);
        }

        return view('user.edit', [
            'user' => $user,
        ]);
    }

    public function createContact(Request $request, User $user): View|RedirectResponse
    {
        abort_unless($this->canEditUser($user, $request->user()), 403);

        if ($request->isMethod('post')) {
            $data = $this->validateContact($request);

            UserContact::create([
                'user' => $user->id,
                'protocol' => (int) $data['protocol'],
                'contact' => trim($data['contact']),
            ]);

            return redirect()->route('user.view', ['user' => $user->id]);
        }

        return view('user.createcontact', [
            'protocols' => Protocol::query()->orderBy('name')->get(),
            'user' => $user,
        ]);
    }

    public function editContact(Request $request, UserContact $contact): View|RedirectResponse
    {
        $contact->load(['protocolModel', 'userModel']);
        $user = $contact->userModel;
        abort_unless($user && $this->canEditUser($user, $request->user()), 403);

        if ($request->isMethod('post')) {
            $data = $this->validateContact($request);

            $contact->update([
                'protocol' => (int) $data['protocol'],
                'contact' => trim($data['contact']),
            ]);

            return redirect()->route('user.view', ['user' => $user->id]);
        }

        return view('user.editcontact', [
            'contact' => $contact,
            'protocols' => Protocol::query()->orderBy('name')->get(),
        ]);
    }

    public function deleteContact(Request $request, UserContact $contact): View|RedirectResponse
    {
        $contact->load(['protocolModel', 'userModel']);
        $user = $contact->userModel;
        abort_unless($user && $this->canEditUser($user, $request->user()), 403);

        if ($request->isMethod('post')) {
            $request->validate(['delete' => ['required', 'accepted']]);
            $contact->delete();

            return redirect()->route('user.view', ['user' => $user->id]);
        }

        return view('user.deletecontact', [
            'contact' => $contact,
        ]);
    }

    public function createCharacter(Request $request, User $user): View|RedirectResponse
    {
        $this->requireUser();
        abort_unless($this->permissionService->allowsOwn('createcharacter', $user, $user->id, $request->user()), 403);

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:85'],
            ]);

            $character = Character::create([
                'name' => trim($data['name']),
                'regdate' => now()->timestamp,
                'user' => $user->id,
                'usertext' => '',
            ]);

            return redirect()->route('user.edit_character', ['character' => $character->id]);
        }

        return view('user.createcharacter', [
            'user' => $user,
        ]);
    }

    public function character(Character $character): View
    {
        $character->load([
            'companies',
            'inventory.itemModel',
            'territories',
            'userModel',
        ]);

        $this->setLocation($character);

        $viewer = auth()->user();
        $ownerId = $character->userModel?->id;
        $canEdit = $viewer && ($viewer->id === $ownerId || $this->permissionService->allows('editcharacter', $character, $viewer));

        return view('user.character', [
            'canEdit' => $canEdit,
            'character' => $character,
        ]);
    }

    public function editCharacter(Request $request, Character $character): View|RedirectResponse
    {
        $character->load(['companies', 'inventory.itemModel', 'userModel']);
        $this->setLocation($character);

        $ownerId = $character->userModel?->id;
        abort_unless(
            $request->user()
            && ($request->user()->id === $ownerId || $this->permissionService->allows('editcharacter', $character, $request->user())),
            403
        );

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'usertext' => ['nullable', 'string'],
                'gender' => ['nullable', 'integer', 'in:0,1,2'],
                'birthday' => ['nullable', 'integer', 'min:0'],
                'location' => ['nullable', 'string', 'max:255'],
                'interests' => ['nullable', 'string', 'max:255'],
                'work' => ['nullable', 'string', 'max:255'],
                'changeavatar' => ['nullable', 'boolean'],
                'avatar' => ['nullable', 'file', 'mimes:jpg,jpeg,gif,png', 'max:500'],
            ]);

            $update = [
                'usertext' => $data['usertext'] ?? '',
                'gender' => (int) ($data['gender'] ?? 0),
                'birthday' => (int) ($data['birthday'] ?? 0),
                'location' => $data['location'] ?? '',
                'interests' => $data['interests'] ?? '',
                'work' => $data['work'] ?? '',
            ];

            if ($request->boolean('changeavatar') && $request->hasFile('avatar')) {
                $this->storeAvatar($character, $request->file('avatar'));
                $update['avatar'] = 1;
            }

            $character->update($update);

            return redirect()->route('user.character', ['character' => $character->id]);
        }

        return view('user.editcharacter', [
            'avatarMaxKilobytes' => 500,
            'character' => $character,
        ]);
    }

    private function applyOrder(Builder $query, string $order): void
    {
        $fields = [
            'post' => 'post__total',
            'name' => 'LOWER(name)',
            'postsperday' => '(post__total / GREATEST(((UNIX_TIMESTAMP() - regdate) / 86400), 1))',
            'regdate' => 'regdate',
            'lastvisit' => 'lastvisit',
        ];

        foreach (explode(';', $order) as $part) {
            [$field, $direction] = array_pad(explode(',', $part, 2), 2, 'a');

            if (! isset($fields[$field])) {
                continue;
            }

            $direction = $direction === 'd' ? 'desc' : 'asc';
            $query->orderByRaw($fields[$field] . ' ' . $direction);
        }
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

    private function validateContact(Request $request): array
    {
        return $request->validate([
            'protocol' => ['required', 'integer', 'exists:dra_protocol,id'],
            'contact' => ['required', 'string', 'max:255'],
        ]);
    }

    private function canEditUser(User $user, ?User $viewer): bool
    {
        return $viewer && ($viewer->id === $user->id || $this->permissionService->allows('edituser', $user, $viewer));
    }

    private function storeAvatar(Character $character, UploadedFile $file): void
    {
        $disk = Storage::disk('public');
        $disk->makeDirectory('character-avatars');
        $disk->makeDirectory('character-avatars/thumb');

        if (! function_exists('imagecreatetruecolor')) {
            $file->storeAs('character-avatars', $character->id . '.jpg', 'public');
            $file->storeAs('character-avatars/thumb', $character->id . '.jpg', 'public');

            return;
        }

        try {
            $manager = ImageManager::gd();
            $source = $file->getRealPath();

            $disk->put(
                'character-avatars/' . $character->id . '.jpg',
                (string) $manager->read($source)
                    ->scaleDown(width: 200, height: 200)
                    ->toJpeg(90)
            );
            $disk->put(
                'character-avatars/thumb/' . $character->id . '.jpg',
                (string) $manager->read($source)
                    ->cover(width: 60, height: 60)
                    ->toJpeg(90)
            );
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'avatar' => 'Der Avatar konnte nicht verarbeitet werden.',
            ]);
        }
    }

    private function requireUser(): void
    {
        abort_unless(auth()->check(), 403);
    }
}
