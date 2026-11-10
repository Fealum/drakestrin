<?php

namespace App\Repositories\Territory;

use App\Models\Territory\Location;
use Illuminate\Support\Collection;

class LocationRepository
{
    public function composerChoices(string $search, int $page = 1, ?int $selected = null): array
    {
        $matches = Location::query()->when(trim($search) !== '', fn ($query) => $query->where('name', 'like', '%'.trim($search).'%'))
            ->orderBy('priority')->orderByRaw('LOWER(name)')->orderBy('id')
            ->paginate(20, ['id', 'name'], 'page', max(1, $page));
        $choices = $matches->getCollection();
        if ($selected && ! $choices->contains('id', $selected)) {
            if ($location = Location::find($selected, ['id', 'name'])) {
                $choices->prepend($location);
            }
        }

        return [
            'matches' => $matches,
            'options' => $choices->map(fn (Location $location) => [
                'value' => (string) $location->id,
                'label' => $location->name,
                'icon' => 'map-marker',
            ])->all(),
        ];
    }

    public function options(): Collection
    {
        return Location::query()
            ->with('parent')
            ->orderBy('priority')
            ->orderByRaw('LOWER(name)')
            ->get()
            ->map(fn (Location $location) => [
                'id' => $location->id,
                'label' => $this->path($location),
            ])
            ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /** @return list<int> */
    public function ancestorLocationIds(Location $location): array
    {
        $ids = [(int) $location->id];
        $parent = $location->parent;

        while ($parent instanceof Location) {
            $ids[] = (int) $parent->id;
            $parent = $parent->parent;
        }

        return $ids;
    }

    private function path(Location $location): string
    {
        $parts = [$location->name];
        $parent = $location->parent;

        while ($parent instanceof Location) {
            array_unshift($parts, $parent->name);
            $parent = $parent->parent;
        }

        if ($parent && isset($parent->name)) {
            array_unshift($parts, $parent->name);
        }

        return implode(' / ', $parts);
    }
}
