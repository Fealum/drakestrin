<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\DB;
use RuntimeException;

trait HasSpatialGeometry
{
    /**
     * Return a spatial column as a decoded GeoJSON geometry array.
     */
    public function geometryJson(string $column = 'geom'): ?array
    {
        if (! in_array($column, $this->spatialColumns(), true)) {
            throw new RuntimeException(sprintf('Unsupported spatial column [%s].', $column));
        }

        if (! $this->getKey()) {
            return null;
        }

        $json = DB::table($this->getTable())
            ->where($this->getKeyName(), $this->getKey())
            ->selectRaw(sprintf('ST_AsGeoJSON(`%s`) as geojson', $column))
            ->value('geojson');

        return $json ? json_decode($json, true) : null;
    }

    protected function spatialColumns(): array
    {
        return ['geom'];
    }
}
