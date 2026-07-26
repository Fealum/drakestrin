<?php

namespace App\Http\Controllers;

use App\Models\Territory\Location;
use App\Models\Territory\Settlement;
use App\Models\Territory\Territory;
use App\Repositories\Economy\TransferRepository;
use App\Services\PermissionService;
use App\Support\PermissionEntityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function __construct(
        PermissionService $permissionService,
        private TransferRepository $transfers,
    ) {
        parent::__construct($permissionService);
    }

    public function view(Location $location): View
    {
        $location->load([
            'children',
            'creator',
            'inventory.item',
            'parent',
            'threadScenes.thread',
            'companySites.company.character',
        ]);

        $this->setLocation($location);

        return view('location.view', [
            'canCreateChild' => auth()->check() && auth()->user()->can('create', [Location::class, $location]),
            'canDelete' => auth()->check() && auth()->user()->can('delete', $location),
            'canEdit' => auth()->check() && auth()->user()->can('update', $location),
            'location' => $location,
            'transfers' => $this->transfers->paginateForParticipant(PermissionEntityType::LOCATION, $location->id),
        ]);
    }

    public function create(Request $request, string $parentType, int|string $parentId): View|RedirectResponse
    {
        $parent = $this->resolveParent($parentType, (int) $parentId);
        $this->authorize('create', [Location::class, $parent]);

        if ($request->isMethod('post')) {
            $data = $this->validateLocation($request);

            $location = Location::create([
                'parent_type' => PermissionEntityType::fromModel($parent)->value,
                'parent_id' => $parent->id,
                'created_by_user_id' => $request->user()->id,
                'name' => trim($data['name']),
                'description' => isset($data['description']) ? trim($data['description']) : null,
                'priority' => (int) ($data['priority'] ?? 0),
            ]);

            return redirect()->route('location.view', ['location' => $location->id]);
        }

        return view('location.create', [
            'parent' => $parent,
        ]);
    }

    public function edit(Request $request, Location $location): View|RedirectResponse
    {
        $this->authorize('update', $location);
        $location->load('parent');

        if ($request->isMethod('post')) {
            $data = $this->validateLocation($request, includeParent: true);
            $parent = $this->resolveParentKey($data['parent']);

            if ($this->parentKey($location->parent) !== $this->parentKey($parent)) {
                $this->authorize('create', [Location::class, $parent]);
            }

            if ($this->wouldCreateCycle($location, $parent)) {
                return back()
                    ->withInput()
                    ->withErrors(['parent' => 'Ein Ort kann nicht sich selbst oder einem eigenen Unterort zugeordnet werden.']);
            }

            $location->update([
                'parent_type' => PermissionEntityType::fromModel($parent)->value,
                'parent_id' => $parent->id,
                'name' => trim($data['name']),
                'description' => isset($data['description']) ? trim($data['description']) : null,
                'priority' => (int) ($data['priority'] ?? 0),
            ]);

            return redirect()->route('location.view', ['location' => $location->id]);
        }

        return view('location.edit', [
            'location' => $location,
            'parentOptions' => $this->parentOptions($location),
            'selectedParent' => $this->parentKey($location->parent),
        ]);
    }

    public function delete(Request $request, Location $location): View|RedirectResponse
    {
        $this->authorize('delete', $location);
        $location->load(['children', 'inventory.item', 'parent']);

        if ($request->isMethod('post')) {
            if ($location->children()->exists() || $location->inventory()->exists()) {
                return back()->withErrors(['location' => 'Dieser Ort kann nicht gelöscht werden, solange er Unterorte oder Inventar enthält.']);
            }

            $parent = $location->parent;
            $location->delete();

            if ($parent instanceof Location) {
                return redirect()->route('location.view', ['location' => $parent->id]);
            }

            if ($parent instanceof Territory) {
                return redirect()->route('territory.view', ['territory' => $parent->id]);
            }

            return redirect()->route('territory');
        }

        return view('location.delete', [
            'location' => $location,
        ]);
    }

    private function validateLocation(Request $request, bool $includeParent = false): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'integer', 'min:-999999', 'max:999999'],
        ];

        if ($includeParent) {
            $rules['parent'] = ['required', 'string'];
        }

        return $request->validate($rules);
    }

    private function resolveParent(string $type, int $id): Model
    {
        return match ($type) {
            'territory' => Territory::findOrFail($id),
            'settlement' => Settlement::findOrFail($id),
            'location' => Location::findOrFail($id),
            default => abort(404),
        };
    }

    private function resolveParentKey(string $key): Model
    {
        [$type, $id] = array_pad(explode(':', $key, 2), 2, null);

        return $this->resolveParent((string) $type, (int) $id);
    }

    private function parentKey(?Model $parent): ?string
    {
        return match (true) {
            $parent instanceof Territory => 'territory:'.$parent->id,
            $parent instanceof Settlement => 'settlement:'.$parent->id,
            $parent instanceof Location => 'location:'.$parent->id,
            default => null,
        };
    }

    private function parentOptions(?Location $editedLocation = null): Collection
    {
        $territories = Territory::query()
            ->whereDoesntHave('children')
            ->orderBy('type')
            ->orderByRaw('LOWER(name)')
            ->get()
            ->map(fn (Territory $territory) => [
                'key' => 'territory:'.$territory->id,
                'label' => 'Gebiet: '.$territory->displayName(),
            ]);

        $settlements = Settlement::query()
            ->orderBy('priority')
            ->orderByRaw('LOWER(name)')
            ->get()
            ->map(fn (Settlement $settlement) => [
                'key' => 'settlement:'.$settlement->id,
                'label' => 'Siedlung: '.$settlement->name,
            ]);

        $locations = Location::query()
            ->orderBy('priority')
            ->orderByRaw('LOWER(name)')
            ->get()
            ->reject(fn (Location $location) => $editedLocation && $this->wouldCreateCycle($editedLocation, $location))
            ->map(fn (Location $location) => [
                'key' => 'location:'.$location->id,
                'label' => 'Ort: '.$location->name,
            ]);

        return $territories->merge($settlements)->merge($locations)->values();
    }

    private function wouldCreateCycle(Location $location, Model $parent): bool
    {
        while ($parent instanceof Location) {
            if ((int) $parent->id === (int) $location->id) {
                return true;
            }

            $parent = $parent->parent;
        }

        return false;
    }
}
