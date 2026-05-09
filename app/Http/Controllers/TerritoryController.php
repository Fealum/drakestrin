<?php

namespace App\Http\Controllers;

use App\Models\Territory\Territory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TerritoryController extends Controller
{
    public function index(): View
    {
        return $this->view(Territory::query()->findOrFail(1));
    }

    public function view(Territory $territory): View
    {
        $territory->load([
            'capital',
            'children.capital',
            'children.children',
            'children.ruler',
            'parent',
            'ruler',
        ]);

        $this->setLocation($territory);

        return view('territory.view', [
            'territory' => $territory,
            'hasMapData' => $territory->children->contains(fn (Territory $child) => $child->geometryJson() !== null),
        ]);
    }

    public function childrenGeoJson(Territory $territory): JsonResponse
    {
        $territory->load('children');

        return response()->json($this->featureCollection(
            $territory->children,
            fn (Territory $child) => [
                'id' => $child->id,
                'name' => $child->name,
            ],
            fn (Territory $child) => $child->geometryJson(),
        ));
    }

    public function settlementsGeoJson(Territory $territory): JsonResponse
    {
        $territory->load('children.capital');

        return response()->json($this->featureCollection(
            $territory->children,
            fn (Territory $child) => [
                'id' => $child->id,
                'name' => $child->name,
                'area' => $child->area,
                'population' => $child->population,
                'capital' => $child->capital?->name,
            ],
            fn (Territory $child) => $child->capital?->geometryJson(),
        ));
    }

    public function landGeoJson(): JsonResponse
    {
        return response()->json(
            json_decode(file_get_contents(public_path('js/ajax__getterritoryland.json')), true)
        );
    }

    private function featureCollection(iterable $models, callable $properties, callable $geometry): array
    {
        $features = [];

        foreach ($models as $model) {
            $features[] = [
                'type' => 'Feature',
                'properties' => $properties($model),
                'geometry' => $geometry($model),
            ];
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }
}
