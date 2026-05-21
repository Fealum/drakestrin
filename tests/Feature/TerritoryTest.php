<?php

namespace Tests\Feature;

use App\Models\Territory\Location;
use App\Models\Territory\Settlement;
use App\Models\Territory\Territory;
use App\Models\User;
use App\Services\PermissionService;
use App\Support\PermissionEntityType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TerritoryTest extends TestCase
{
    private string $prefix;
    private int $capitalId;
    private int $childCapitalId;
    private int $territoryId;
    private int $childTerritoryId;
    private int $locationId;
    private int $childLocationId;
    private int $userId;
    private array $originalLocationPermitStandards = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->prefix = 'ctt_' . substr(str_replace('.', '_', uniqid('', true)), 0, 12);
        $this->originalLocationPermitStandards = DB::table('permits')
            ->whereIn('name', ['createlocation', 'editlocation', 'deletelocation'])
            ->pluck('standard', 'name')
            ->all();

        $timestamp = now()->subDay()->timestamp;
        $this->userId = DB::table('users')->insertGetId([
            'name' => $this->prefix . '_user',
            'password' => 'secret',
            'email' => $this->prefix . '_user@example.test',
            'regemail' => $this->prefix . '_user@example.test',
            'regdate' => $timestamp,
            'lastvisit' => $timestamp,
            'lastactivity' => $timestamp,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'wohnort' => '',
        ]);

        $capital = Settlement::factory()->create([
            'name' => $this->prefix . '_capital',
            'population' => 1234,
            'priority' => 1,
        ]);
        $this->capitalId = $capital->id;

        $childCapital = Settlement::factory()->create([
            'name' => $this->prefix . '_child_capital',
            'population' => 2345,
            'priority' => 1,
        ]);
        $this->childCapitalId = $childCapital->id;

        $territory = Territory::factory()->create([
            'name' => $this->prefix . '_province',
            'type' => '2',
            'parent_id' => 1,
            'character_id' => 0,
            'area' => 100000000,
            'population' => 1000,
            'geldstand' => 0,
            'beliebtheit' => 50,
            'capital_id' => $this->capitalId,
        ]);
        $this->territoryId = $territory->id;

        $childTerritory = Territory::factory()->create([
            'name' => $this->prefix . '_county',
            'type' => '3b',
            'parent_id' => $this->territoryId,
            'character_id' => 0,
            'area' => 50000000,
            'population' => 500,
            'geldstand' => 0,
            'beliebtheit' => 50,
            'capital_id' => $this->childCapitalId,
        ]);
        $this->childTerritoryId = $childTerritory->id;

        $location = Location::factory()->create([
            'parent_type' => PermissionEntityType::TERRITORY->value,
            'parent_id' => $this->childTerritoryId,
            'name' => $this->prefix . '_market_square',
            'description' => $this->prefix . '_market_description',
            'priority' => 1,
        ]);
        $this->locationId = $location->id;

        $childLocation = Location::factory()->create([
            'parent_type' => PermissionEntityType::LOCATION->value,
            'parent_id' => $this->locationId,
            'name' => $this->prefix . '_market_stall',
            'description' => $this->prefix . '_stall_description',
            'priority' => 1,
        ]);
        $this->childLocationId = $childLocation->id;

        DB::statement('UPDATE territories SET geom = ST_GeomFromText(?) WHERE id = ?', [
            'MULTIPOLYGON(((0 0,0 1,1 1,1 0,0 0)))',
            $this->childTerritoryId,
        ]);

        DB::statement('UPDATE settlements SET geom = ST_GeomFromText(?) WHERE id = ?', [
            'POINT(0.5 0.5)',
            $this->childCapitalId,
        ]);
    }

    protected function tearDown(): void
    {
        DB::table('territories')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        DB::table('locations')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        DB::table('settlements')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        DB::table('onlines')->where('user_id', $this->userId)->delete();
        DB::table('users')->where('id', $this->userId)->delete();

        foreach ($this->originalLocationPermitStandards as $name => $standard) {
            DB::table('permits')->where('name', $name)->update(['standard' => $standard]);
        }

        Cache::forget('user_permits:' . $this->userId);
        Cache::forget('user_permissions:' . $this->userId);
        Cache::forget('user_permits:global');
        Cache::forget('user_permissions:global');

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

        $childResponse = $this->get('/territory/view/' . $this->childTerritoryId);

        $childResponse->assertOk();
        $childResponse->assertSee($this->prefix . '_market_square');
        $childResponse->assertSee('/location/view/' . $this->locationId, false);
    }

    public function test_location_page_renders_parent_and_child_locations(): void
    {
        $response = $this->get('/location/view/' . $this->locationId);

        $response->assertOk();
        $response->assertSee($this->prefix . '_market_square');
        $response->assertSee($this->prefix . '_market_description');
        $response->assertSee('Grafschaft ' . $this->prefix . '_county');
        $response->assertSee($this->prefix . '_market_stall');
        $response->assertSee('/location/view/' . $this->childLocationId, false);
    }

    public function test_location_create_requires_permission(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $this->get('/location/create/territory/' . $this->territoryId)->assertForbidden();
    }

    public function test_location_create_is_limited_to_leaf_territories(): void
    {
        $this->setLocationPermit('createlocation', 2);
        $this->actingAs(User::findOrFail($this->userId));

        $this->get('/location/create/territory/' . $this->territoryId)->assertForbidden();

        $this->get('/location/create/territory/' . $this->childTerritoryId)->assertOk();
    }

    public function test_location_can_be_created_and_edited_by_creator_when_permissions_allow_it(): void
    {
        $this->setLocationPermit('createlocation', 2);
        $this->setLocationPermit('editlocation', 1);
        $this->actingAs(User::findOrFail($this->userId));

        $create = $this->post('/location/create/territory/' . $this->childTerritoryId, [
            'name' => $this->prefix . '_created_location',
            'description' => $this->prefix . '_created_description',
            'priority' => 7,
        ]);

        $createdLocationId = (int) DB::table('locations')
            ->where('name', $this->prefix . '_created_location')
            ->value('id');

        $create->assertRedirect('/location/view/' . $createdLocationId);
        $this->assertDatabaseHas('locations', [
            'id' => $createdLocationId,
            'parent_type' => PermissionEntityType::TERRITORY->value,
            'parent_id' => $this->childTerritoryId,
            'created_by_user_id' => $this->userId,
            'description' => $this->prefix . '_created_description',
            'priority' => 7,
        ]);

        $edit = $this->get('/location/edit/' . $createdLocationId);
        $edit->assertOk();
        $edit->assertSee($this->prefix . '_created_location');

        $update = $this->post('/location/edit/' . $createdLocationId, [
            'parent' => 'territory:' . $this->childTerritoryId,
            'name' => $this->prefix . '_edited_location',
            'description' => $this->prefix . '_edited_description',
            'priority' => 8,
        ]);

        $update->assertRedirect('/location/view/' . $createdLocationId);
        $this->assertDatabaseHas('locations', [
            'id' => $createdLocationId,
            'name' => $this->prefix . '_edited_location',
            'description' => $this->prefix . '_edited_description',
            'priority' => 8,
        ]);
    }

    public function test_location_delete_requires_high_permission_and_refuses_non_empty_locations(): void
    {
        $this->setLocationPermit('deletelocation', 1);
        $this->actingAs(User::findOrFail($this->userId));

        $this->get('/location/delete/' . $this->locationId)->assertForbidden();

        $this->setLocationPermit('deletelocation', 2);

        $deleteForm = $this->get('/location/delete/' . $this->locationId);
        $deleteForm->assertOk();
        $deleteForm->assertSee('Unterorte');

        $blockedDelete = $this->post('/location/delete/' . $this->locationId);
        $blockedDelete->assertSessionHasErrors('location');
        $this->assertDatabaseHas('locations', ['id' => $this->locationId]);

        $emptyLocation = Location::factory()->create([
            'parent_type' => PermissionEntityType::TERRITORY->value,
            'parent_id' => $this->childTerritoryId,
            'name' => $this->prefix . '_empty_location',
            'description' => '',
            'priority' => 1,
        ]);

        $delete = $this->post('/location/delete/' . $emptyLocation->id);
        $delete->assertRedirect('/territory/view/' . $this->childTerritoryId);
        $this->assertDatabaseMissing('locations', ['id' => $emptyLocation->id]);
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

    private function setLocationPermit(string $name, int $standard): void
    {
        DB::table('permits')->where('name', $name)->update(['standard' => $standard]);
        Cache::forget('user_permits:' . $this->userId);
        Cache::forget('user_permissions:' . $this->userId);
        app()->forgetInstance(PermissionService::class);
    }
}
