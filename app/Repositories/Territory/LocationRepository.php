<?php

namespace App\Repositories\Territory;

use App\Models\Territory\Location;
use Illuminate\Support\Collection;

class LocationRepository
{
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
