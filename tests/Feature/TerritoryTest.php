<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TerritoryTest extends TestCase
{
    private string $prefix;
    private int $capitalId;
    private int $childCapitalId;
    private int $territoryId;
    private int $childTerritoryId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prefix = 'ctt_' . substr(str_replace('.', '_', uniqid('', true)), 0, 12);

        $this->capitalId = DB::table('dra_settlement')->insertGetId([
            'name' => $this->prefix . '_capital',
            'population' => 1234,
            'priority' => 1,
        ]);

        $this->childCapitalId = DB::table('dra_settlement')->insertGetId([
            'name' => $this->prefix . '_child_capital',
            'population' => 2345,
            'priority' => 1,
        ]);

        $this->territoryId = DB::table('dra_territory')->insertGetId([
            'name' => $this->prefix . '_province',
            'type' => '2',
            'territory' => 1,
            'character' => 0,
            'area' => 100000000,
            'population' => 1000,
            'geldstand' => 0,
            'beliebtheit' => 50,
            'settlement' => $this->capitalId,
        ]);

        $this->childTerritoryId = DB::table('dra_territory')->insertGetId([
            'name' => $this->prefix . '_county',
            'type' => '3b',
            'territory' => $this->territoryId,
            'character' => 0,
            'area' => 50000000,
            'population' => 500,
            'geldstand' => 0,
            'beliebtheit' => 50,
            'settlement' => $this->childCapitalId,
        ]);

        DB::statement('UPDATE dra_territory SET geom = ST_GeomFromText(?) WHERE id = ?', [
            'MULTIPOLYGON(((0 0,0 1,1 1,1 0,0 0)))',
            $this->childTerritoryId,
        ]);

        DB::statement('UPDATE dra_settlement SET geom = ST_GeomFromText(?) WHERE id = ?', [
            'POINT(0.5 0.5)',
            $this->childCapitalId,
        ]);
    }

    protected function tearDown(): void
    {
        DB::table('dra_territory')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        DB::table('dra_settlement')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        parent::tearDown();
    }

    public function test_territory_page_renders_read_model_and_progressive_map_enhancement(): void
    {
        $response = $this->get('/territory/view/' . $this->territoryId);

        $response->assertOk();
        $response->assertSee('Provinz ' . $this->prefix . '_province');
        $response->assertSee('Grafschaft ' . $this->prefix . '_county');
        $response->assertSee($this->prefix . '_capital');
        $response->assertSee('x-data="territoryMap', false);
        $response->assertSee('js/alpine.min.js', false);
        $response->assertSee('js/d3.v4.min.js', false);
        $response->assertSee('children.geojson', false);
        $response->assertSee('settlements.geojson', false);
        $response->assertSee('images/territory/' . $this->territoryId . '.png', false);
        $response->assertSee('images/territory', false);
        $response->assertDontSee('d3js.org', false);
        $response->assertDontSee('ajax__getterritories', false);
        $response->assertDontSee('img/territory.id', false);
    }

    public function test_territory_geojson_endpoints_return_children_and_settlement_data(): void
    {
        $territories = $this->getJson('/territory/' . $this->territoryId . '/children.geojson');

        $territories->assertOk();
        $territories->assertJsonPath('type', 'FeatureCollection');
        $territories->assertJsonPath('features.0.properties.id', $this->childTerritoryId);
        $territories->assertJsonPath('features.0.properties.name', $this->prefix . '_county');
        $territories->assertJsonPath('features.0.geometry.type', 'MultiPolygon');

        $territoryData = $this->getJson('/territory/' . $this->territoryId . '/settlements.geojson');

        $territoryData->assertOk();
        $territoryData->assertJsonPath('features.0.properties.capital', $this->prefix . '_child_capital');
        $territoryData->assertJsonPath('features.0.geometry.type', 'Point');
    }

    public function test_territory_land_endpoint_serves_static_geojson(): void
    {
        $response = $this->getJson('/territory/land.geojson');

        $response->assertOk();
        $response->assertJsonPath('type', 'FeatureCollection');
    }

    public function test_legacy_territory_ajax_endpoints_are_not_routed_anymore(): void
    {
        $this->get('/territory/ajax__getterritories/' . $this->territoryId)->assertNotFound();
        $this->get('/territory/ajax__getterritorydata/' . $this->territoryId)->assertNotFound();
        $this->get('/territory/ajax__getterritoryland')->assertNotFound();
    }

    public function test_legacy_coat_of_arms_url_redirects_to_public_laravel_asset(): void
    {
        $this->get('/img/territory.id/1.png')
            ->assertRedirect('/images/territory/1.png');
    }
}
