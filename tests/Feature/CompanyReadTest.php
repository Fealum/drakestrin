<?php

namespace Tests\Feature;

use App\Models\Economy\CompanyWorker;
use App\Models\Economy\Inventory;
use App\Models\User;
use App\Services\Economy\LabourProcessor;
use App\Support\Currency;
use App\Support\PermissionEntityType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CompanyReadTest extends TestCase
{
    private string $prefix;

    private int $userId;

    private int $otherUserId;

    private int $characterId;

    private int $companyId;

    private int $companySiteId;

    private int $workerId;

    private int $itemId;

    private int $toolItemId;

    private int $outputItemId;

    private int $labourId;

    private int $locationId;

    private int $timestamp;

    protected function setUp(): void
    {
        parent::setUp();
        config(['economy.process_labour_on_request' => false]);

        $this->prefix = '000_ct_company_'.substr(str_replace('.', '_', uniqid('', true)), 0, 8);
        $this->timestamp = now()->subDays(10)->timestamp;

        $this->userId = DB::table('users')->insertGetId([
            'name' => $this->prefix.'_owner',
            'password' => 'secret',
            'email' => $this->prefix.'_owner@example.test',
            'regemail' => $this->prefix.'_owner@example.test',
            'regdate' => $this->timestamp,
            'lastvisit' => $this->timestamp,
            'lastactivity' => $this->timestamp,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'wohnort' => '',
        ]);

        $this->otherUserId = DB::table('users')->insertGetId([
            'name' => $this->prefix.'_visitor',
            'password' => 'secret',
            'email' => $this->prefix.'_visitor@example.test',
            'regemail' => $this->prefix.'_visitor@example.test',
            'regdate' => $this->timestamp,
            'lastvisit' => $this->timestamp,
            'lastactivity' => $this->timestamp,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'wohnort' => '',
        ]);

        $this->characterId = DB::table('characters')->insertGetId([
            'name' => $this->prefix.'_character',
            'post_count' => 0,
            'regdate' => $this->timestamp,
            'birthday' => 0,
            'interests' => '',
            'location' => '',
            'work' => '',
            'gender' => 0,
            'usertext' => '',
            'user_id' => $this->userId,
        ]);

        $territoryId = (int) DB::table('territories')->value('id');

        $this->locationId = DB::table('locations')->insertGetId([
            'parent_type' => PermissionEntityType::TERRITORY->value,
            'parent_id' => $territoryId,
            'created_by_user_id' => $this->userId,
            'name' => $this->prefix.'_location',
            'description' => '',
            'priority' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->companyId = DB::table('companies')->insertGetId([
            'name' => $this->prefix.'_company',
            'type' => 2,
            'description' => $this->prefix.'_description',
            'territory_id' => $territoryId,
            'thread_id' => 0,
            'volksgeld' => 0,
        ]);

        DB::table('company_owners')->insert([
            'company_id' => $this->companyId,
            'character_id' => $this->characterId,
            'added_by_user_id' => $this->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->companySiteId = DB::table('company_sites')->insertGetId([
            'company_id' => $this->companyId,
            'location_id' => $this->locationId,
            'name' => 'Hauptstandort',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('companies')->where('id', $this->companyId)->update([
            'headquarters_site_id' => $this->companySiteId,
        ]);

        $this->workerId = DB::table('company_workers')->insertGetId([
            'name' => $this->prefix.'_worker',
            'type' => 3,
            'company_id' => $this->companyId,
            'company_site_id' => $this->companySiteId,
            'hired' => $this->timestamp,
            'paid' => $this->timestamp,
        ]);

        $this->itemId = DB::table('items')->insertGetId([
            'name' => $this->prefix.'_item',
            'stackable' => 1,
            'description' => '',
            'img' => 1,
            'weight' => 1,
            'unit' => '',
        ]);

        $this->toolItemId = DB::table('items')->insertGetId([
            'name' => $this->prefix.'_tool',
            'stackable' => 0,
            'description' => '',
            'img' => 1,
            'weight' => 1,
            'unit' => '',
        ]);

        $this->outputItemId = DB::table('items')->insertGetId([
            'name' => $this->prefix.'_output',
            'stackable' => 1,
            'description' => '',
            'img' => 1,
            'weight' => 1,
            'unit' => '',
        ]);

        DB::table('inventories')->insert([
            'item_id' => $this->itemId,
            'stack' => 3,
            'wear' => -2,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $this->labourId = DB::table('labours')->insertGetId([
            'name' => $this->prefix.'_labour',
            'type' => 2,
            'duration' => 120,
            'workload' => 2,
            'benwerkzeug' => '',
            'benrohstoff' => '',
            'ergebnis' => '',
        ]);

        DB::table('labour_actives')->insert([
            'company_worker_id' => $this->workerId,
            'labour_id' => $this->labourId,
            'since' => $this->timestamp,
            'until' => $this->timestamp + 120,
            'prodas' => -2,
            'quantity' => 1,
            'instances' => 1,
            'nextinsta' => 0,
        ]);
    }

    protected function tearDown(): void
    {
        $companyIds = DB::table('companies')
            ->where('name', 'like', $this->prefix.'%')
            ->pluck('id');

        DB::table('company_representatives')->whereIn('company_id', $companyIds)->delete();
        DB::table('company_role_events')->whereIn('company_id', $companyIds)->delete();
        DB::table('company_owners')->whereIn('company_id', $companyIds)->delete();
        DB::table('companies')->whereIn('id', $companyIds)->update(['headquarters_site_id' => null]);
        DB::table('company_sites')->whereIn('company_id', $companyIds)->delete();
        DB::table('locations')->where('name', 'like', $this->prefix.'%')->delete();
        DB::table('inventory_mutations')->whereIn('item_id', [$this->itemId, $this->toolItemId, $this->outputItemId])->delete();
        DB::table('production_runs')->where('company_id', $this->companyId)->delete();
        DB::table('labour_actives')->where('company_worker_id', $this->workerId)->delete();
        DB::table('labour_components')
            ->where('labour_id', $this->labourId)
            ->orWhereIn('item_id', [$this->itemId, $this->toolItemId, $this->outputItemId])
            ->delete();
        DB::table('labours')->where('id', $this->labourId)->delete();
        DB::table('inventories')->where('owner_id', $this->companySiteId)->where('owner_type', PermissionEntityType::COMPANY_SITE->value)->delete();
        DB::table('items')->whereIn('id', [$this->itemId, $this->toolItemId, $this->outputItemId])->delete();
        DB::table('company_workers')->where('company_id', $this->companyId)->delete();
        DB::table('companies')->whereIn('id', $companyIds)->delete();
        DB::table('permissions')->where('recipient_type', PermissionEntityType::USER->value)->where('recipient_id', $this->userId)->delete();
        DB::table('characters')->where('name', 'like', $this->prefix.'%')->delete();
        DB::table('onlines')->whereIn('user_id', [$this->userId, $this->otherUserId])->delete();
        DB::table('users')->whereIn('id', [$this->userId, $this->otherUserId])->delete();
        Cache::forget('user_permits:'.$this->userId);
        Cache::forget('user_permissions:'.$this->userId);

        parent::tearDown();
    }

    public function test_owner_can_create_and_edit_company_at_location(): void
    {

        $this->actingAs(User::findOrFail($this->userId))
            ->get('/company/create')
            ->assertOk()
            ->assertSee('Betrieb gründen');

        $this->post('/company', [
            'name' => $this->prefix.'_new_company',
            'sector' => 3,
            'owner_character_id' => $this->characterId,
            'location_mode' => 'existing',
            'location_id' => $this->locationId,
            'description' => "Kurz\n\nLang",
        ])
            ->assertRedirect();

        $companyId = (int) DB::table('companies')
            ->where('name', $this->prefix.'_new_company')
            ->value('id');

        $this->assertDatabaseHas('company_sites', [
            'company_id' => $companyId,
            'location_id' => $this->locationId,
            'name' => 'Hauptstandort',
        ]);
        $this->assertSame(
            (int) DB::table('company_sites')->where('company_id', $companyId)->value('id'),
            (int) DB::table('companies')->where('id', $companyId)->value('headquarters_site_id'),
        );

        $this->get('/company/edit/'.$companyId)
            ->assertOk()
            ->assertSee('Betrieb bearbeiten');

        $this->put('/company/edit/'.$companyId, [
            'name' => $this->prefix.'_renamed_company',
            'sector' => 4,
            'description' => "Neu\n\nNeuer Text",
        ])->assertRedirect('/company/view/'.$companyId);

        $this->assertDatabaseHas('companies', [
            'id' => $companyId,
            'name' => $this->prefix.'_renamed_company',
            'type' => 4,
        ]);
    }

    public function test_company_can_create_its_world_location_and_headquarters_atomically(): void
    {
        $fautheiId = (int) DB::table('territories')->where('type', 5)->value('id');
        $permitId = (int) DB::table('permits')->where('name', 'createlocation')->value('id');
        DB::table('permissions')->insert([
            'recipient_type' => PermissionEntityType::USER->value,
            'recipient_id' => $this->userId,
            'subject_type' => 0,
            'subject_id' => 0,
            'permit_id' => $permitId,
            'value' => 2,
        ]);
        Cache::forget('user_permits:'.$this->userId);
        Cache::forget('user_permissions:'.$this->userId);

        $this->actingAs(User::findOrFail($this->userId));
        $name = $this->prefix.'_new_place_company';
        $this->post('/company', [
            'name' => $name,
            'sector' => 3,
            'owner_character_id' => $this->characterId,
            'location_mode' => 'new',
            'fauthei_id' => $fautheiId,
            'description' => '',
        ])->assertRedirect();

        $company = DB::table('companies')->where('name', $name)->first();
        $location = DB::table('locations')->where('name', $name)->first();
        $site = DB::table('company_sites')->where('company_id', $company->id)->first();

        $this->assertNotNull($location);
        $this->assertSame($fautheiId, (int) $location->parent_id);
        $this->assertSame(PermissionEntityType::TERRITORY->value, (int) $location->parent_type);
        $this->assertSame('Hauptstandort', $site->name);
        $this->assertSame((int) $site->id, (int) $company->headquarters_site_id);
        $this->assertDatabaseHas('company_owners', [
            'company_id' => $company->id,
            'character_id' => $this->characterId,
        ]);
    }

    public function test_sites_use_tabs_and_headquarters_changes_do_not_move_inventory(): void
    {
        $secondLocationId = DB::table('locations')->insertGetId([
            'parent_type' => PermissionEntityType::TERRITORY->value,
            'parent_id' => (int) DB::table('territories')->where('type', 5)->value('id'),
            'created_by_user_id' => $this->userId,
            'name' => $this->prefix.'_second_location',
            'description' => '',
            'priority' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->actingAs(User::findOrFail($this->userId));

        $this->post('/company/'.$this->companyId.'/sites', [
            'name' => 'Sägewerk',
            'location_id' => $secondLocationId,
        ])->assertRedirect('/company/view/'.$this->companyId);
        $secondSiteId = (int) DB::table('company_sites')
            ->where('company_id', $this->companyId)
            ->where('location_id', $secondLocationId)
            ->value('id');

        $this->get('/company/view/'.$this->companyId)
            ->assertOk()
            ->assertSee('aria-label="Standorte"', false)
            ->assertSee('id="site-'.$this->companySiteId.'"', false)
            ->assertSee('id="site-'.$secondSiteId.'"', false)
            ->assertSee('id="site-'.$this->companySiteId.'-representatives-heading">Standortvertretung', false)
            ->assertSee('id="site-'.$secondSiteId.'-representatives-heading">Standortvertretung', false)
            ->assertSee('name="company_site_id" value="'.$this->companySiteId.'"', false)
            ->assertSee('name="company_site_id" value="'.$secondSiteId.'"', false)
            ->assertDontSee('<select name="company_site_id">', false);

        $this->assertStringContainsString(
            '.company-site-panel:first-child:not(:target)',
            file_get_contents(public_path('css/company_view.css')),
        );

        $this->patch('/company/'.$this->companyId.'/sites/'.$secondSiteId.'/headquarters')
            ->assertRedirect('/company/view/'.$this->companyId);
        $this->assertDatabaseHas('companies', [
            'id' => $this->companyId,
            'headquarters_site_id' => $secondSiteId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
            'item_id' => $this->itemId,
        ]);

        $this->delete('/company/'.$this->companyId.'/sites/'.$this->companySiteId)
            ->assertSessionHasErrors('site');
    }

    public function test_owners_are_equal_and_can_only_transfer_their_own_membership(): void
    {
        $otherCharacterId = $this->createCharacter($this->otherUserId, '_co_owner');
        $this->actingAs(User::findOrFail($this->userId));

        $this->post('/company/'.$this->companyId.'/owners', ['character_id' => $otherCharacterId])
            ->assertRedirect('/company/view/'.$this->companyId);
        $otherOwnerId = (int) DB::table('company_owners')
            ->where('company_id', $this->companyId)
            ->where('character_id', $otherCharacterId)
            ->value('id');
        $ownOwnerId = (int) DB::table('company_owners')
            ->where('company_id', $this->companyId)
            ->where('character_id', $this->characterId)
            ->value('id');

        $this->post('/company/'.$this->companyId.'/owners/'.$otherOwnerId.'/transfer', [
            'target_owner_id' => $ownOwnerId,
        ])->assertForbidden();

        $this->post('/company/'.$this->companyId.'/owners/'.$ownOwnerId.'/transfer', [
            'target_owner_id' => $otherOwnerId,
        ])->assertRedirect('/company/view/'.$this->companyId);
        $this->assertDatabaseMissing('company_owners', ['id' => $ownOwnerId]);
        $this->assertDatabaseHas('company_owners', ['id' => $otherOwnerId]);
    }

    public function test_manager_appointment_hierarchy_and_site_roles_are_enforced(): void
    {
        $managerCharacterId = $this->createCharacter($this->otherUserId, '_hierarchy_manager');
        $foremanCharacterId = $this->createCharacter($this->userId, '_hierarchy_foreman');
        $secondManagerCharacterId = $this->createCharacter($this->userId, '_second_manager');

        $this->actingAs(User::findOrFail($this->userId))
            ->post('/company/'.$this->companyId.'/representatives', [
                'character_id' => $managerCharacterId,
                'role' => 'manager',
            ])->assertRedirect('/company/view/'.$this->companyId);

        $this->actingAs(User::findOrFail($this->otherUserId))
            ->post('/company/'.$this->companyId.'/representatives', [
                'character_id' => $secondManagerCharacterId,
                'role' => 'manager',
            ])->assertForbidden();

        $this->post('/company/'.$this->companyId.'/representatives', [
            'character_id' => $foremanCharacterId,
            'role' => 'foreman',
            'company_site_id' => $this->companySiteId,
        ])->assertRedirect('/company/view/'.$this->companyId);
        $this->assertDatabaseHas('company_representatives', [
            'company_id' => $this->companyId,
            'company_site_id' => $this->companySiteId,
            'character_id' => $foremanCharacterId,
            'role' => 'foreman',
        ]);
    }

    public function test_foreman_manages_only_their_site_operations(): void
    {
        $foremanCharacterId = $this->createCharacter($this->otherUserId, '_foreman');
        $this->actingAs(User::findOrFail($this->userId))
            ->post('/company/'.$this->companyId.'/representatives', [
                'character_id' => $foremanCharacterId,
                'role' => 'foreman',
                'company_site_id' => $this->companySiteId,
            ])->assertRedirect('/company/view/'.$this->companyId);

        $this->actingAs(User::findOrFail($this->otherUserId));
        $this->get('/company/view/'.$this->companyId)
            ->assertOk()
            ->assertSee('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/hire/3', false)
            ->assertSee('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/inventory', false)
            ->assertDontSee('/company/edit/'.$this->companyId, false);
        $this->post('/company/'.$this->companyId.'/sites', [
            'name' => 'Unzulässig',
            'location_id' => $this->locationId,
        ])->assertForbidden();
    }

    public function test_clerk_can_see_but_not_reclassify_site_stock(): void
    {
        $clerkCharacterId = $this->createCharacter($this->otherUserId, '_clerk_role');
        $this->actingAs(User::findOrFail($this->userId))
            ->post('/company/'.$this->companyId.'/representatives', [
                'character_id' => $clerkCharacterId,
                'role' => 'clerk',
                'company_site_id' => $this->companySiteId,
            ])->assertRedirect('/company/view/'.$this->companyId);

        $this->actingAs(User::findOrFail($this->otherUserId))
            ->get('/company/view/'.$this->companyId)
            ->assertOk()
            ->assertSee('images/item/1.png', false)
            ->assertDontSee('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/inventory', false)
            ->assertDontSee('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/hire', false);
    }

    public function test_company_sector_is_locked_after_operations_begin(): void
    {

        $this->actingAs(User::findOrFail($this->userId))
            ->get('/company/edit/'.$this->companyId)
            ->assertOk()
            ->assertSee('name="sector" value="2"', false)
            ->assertSee('<select id="sector"', false)
            ->assertSee('disabled', false)
            ->assertSee('Der Wirtschaftszweig ist nach Einstellung von Beschäftigten oder Beginn der Produktion festgelegt.');

        $this->put('/company/edit/'.$this->companyId, [
            'name' => $this->prefix.'_company',
            'sector' => 3,
            'description' => $this->prefix.'_description',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('sector');

        $this->assertDatabaseHas('companies', [
            'id' => $this->companyId,
            'type' => 2,
        ]);
    }

    public function test_owner_can_appoint_manager_and_manager_can_manage_company(): void
    {
        $managerCharacterId = DB::table('characters')->insertGetId([
            'name' => $this->prefix.'_manager',
            'post_count' => 0,
            'regdate' => $this->timestamp,
            'birthday' => 0,
            'interests' => '',
            'location' => '',
            'work' => '',
            'gender' => 0,
            'usertext' => '',
            'user_id' => $this->otherUserId,
        ]);

        $this->actingAs(User::findOrFail($this->userId))
            ->post('/company/'.$this->companyId.'/representatives', [
                'character_id' => $managerCharacterId,
                'role' => 'manager',
            ])
            ->assertRedirect('/company/view/'.$this->companyId);

        $this->assertDatabaseHas('company_representatives', [
            'company_id' => $this->companyId,
            'character_id' => $managerCharacterId,
            'role' => 'manager',
        ]);

        $this->actingAs(User::findOrFail($this->otherUserId))
            ->get('/company/view/'.$this->companyId)
            ->assertOk()
            ->assertSee('Neuen Beschäftigten einstellen');
    }

    public function test_manager_can_save_multiple_company_inventory_changes_at_once(): void
    {
        $inventoryId = (int) DB::table('inventories')
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->where('owner_id', $this->companySiteId)
            ->where('item_id', $this->itemId)
            ->value('id');

        $reservedInventoryId = DB::table('inventories')->insertGetId([
            'item_id' => $this->outputItemId,
            'stack' => 2,
            'wear' => -1,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $this->actingAs(User::findOrFail($this->userId))
            ->put('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/inventory', [
                'inventory' => [
                    $inventoryId => [
                        'state' => 'sale',
                        'price' => [
                            'til' => '',
                            'tuk' => 2,
                            'ten' => 5000,
                        ],
                        'quantity' => '1',
                    ],
                    $reservedInventoryId => [
                        'state' => 'production',
                        'price' => [
                            'til' => '',
                            'tuk' => '',
                            'ten' => '',
                        ],
                        'quantity' => '2',
                    ],
                ],
            ])
            ->assertRedirect('/company/view/'.$this->companyId);

        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->itemId,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
            'wear' => Currency::toTen(0, 2, 5000),
            'stack' => 1,
        ]);
        $this->assertDatabaseHas('inventories', [
            'id' => $inventoryId,
            'wear' => -2,
            'stack' => 2,
        ]);
        $this->assertDatabaseHas('inventories', [
            'id' => $reservedInventoryId,
            'wear' => -2,
            'stack' => 2,
        ]);

        $page = $this->get('/company/view/'.$this->companyId);

        $page->assertSee('name="inventory['.$inventoryId.'][price][til]"', false);
        $page->assertSee('name="inventory['.$inventoryId.'][price][tuk]"', false);
        $page->assertSee('name="inventory['.$inventoryId.'][price][ten]"', false);
        $page->assertSee('Produktionsgüter');
        $page->assertSee('Vorbehaltsgüter');
        $page->assertSee('Verkaufsgüter');
        $page->assertSee('placeholder="0"', false);
        $page->assertSee('class="inventory-item-quantity"', false);
        $page->assertOk()->assertSee('Inventar speichern');
        $this->assertSame(1, substr_count($page->getContent(), '/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/inventory'));
        $this->assertSame(1, substr_count($page->getContent(), 'Inventar speichern'));
        $page->assertDontSee('>Ändern<', false);
    }

    public function test_company_inventory_normalizes_overflowing_denominations(): void
    {
        $inventoryId = (int) DB::table('inventories')
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->where('owner_id', $this->companySiteId)
            ->where('item_id', $this->itemId)
            ->value('id');

        $this->actingAs(User::findOrFail($this->userId))
            ->from('/company/view/'.$this->companyId)
            ->put('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/inventory', [
                'inventory' => [
                    $inventoryId => [
                        'state' => 'sale',
                        'price' => ['til' => 0, 'tuk' => Currency::TUK_PER_TIL, 'ten' => Currency::TEN_PER_TUK + 1],
                        'quantity' => '1',
                    ],
                ],
            ])
            ->assertRedirect('/company/view/'.$this->companyId)
            ->assertSessionDoesntHaveErrors();

        $normalizedPrice = Currency::toTen(0, Currency::TUK_PER_TIL, Currency::TEN_PER_TUK + 1);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->itemId,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
            'wear' => $normalizedPrice,
            'stack' => 1,
        ]);
        $saleInventoryId = (int) DB::table('inventories')
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->where('owner_id', $this->companySiteId)
            ->where('item_id', $this->itemId)
            ->where('wear', $normalizedPrice)
            ->value('id');

        $page = $this->get('/company/view/'.$this->companyId)->assertOk()->getContent();
        foreach (['til', 'tuk', 'ten'] as $denomination) {
            $name = preg_quote('inventory['.$saleInventoryId.'][price]['.$denomination.']', '/');
            $this->assertMatchesRegularExpression('/name="'.$name.'"[^>]*value="1"/', $page);
        }
    }

    public function test_identical_item_identities_are_grouped_and_can_be_partly_reclassified(): void
    {
        $inventoryIds = collect(range(1, 3))->map(fn () => DB::table('inventories')->insertGetId([
            'item_id' => $this->toolItemId,
            'stack' => 0,
            'wear' => -2,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
            'timelastvalue' => 0,
            'data' => '',
        ]));
        $representativeId = (int) $inventoryIds->first();

        $page = $this->actingAs(User::findOrFail($this->userId))
            ->get('/company/view/'.$this->companyId)
            ->assertOk();

        $this->assertSame(3, substr_count(
            $page->getContent(),
            'name="inventory['.$representativeId.'][members][]"',
        ));

        $this->put('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/inventory', [
            'inventory' => [
                $representativeId => [
                    'state' => 'sale',
                    'price' => ['til' => 1, 'tuk' => 2, 'ten' => 3],
                    'quantity' => '2',
                    'members' => $inventoryIds->all(),
                ],
            ],
        ])->assertRedirect('/company/view/'.$this->companyId);

        $this->assertSame(2, DB::table('inventories')
            ->whereIn('id', $inventoryIds)
            ->where('wear', Currency::toTen(1, 2, 3))
            ->count());
        $this->assertSame(1, DB::table('inventories')
            ->whereIn('id', $inventoryIds)
            ->where('wear', -2)
            ->count());
    }

    public function test_inventory_model_rejects_stacked_item_identities(): void
    {
        $this->expectException(\LogicException::class);

        Inventory::create([
            'item_id' => $this->toolItemId,
            'stack' => 2,
            'wear' => -2,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
            'timelastvalue' => 0,
            'data' => '',
        ]);
    }

    public function test_company_overview_and_detail_render_legacy_shape(): void
    {
        $overview = $this->get('/company');

        $overview->assertOk();
        $overview->assertSee('Kontor');
        $overview->assertSee($this->prefix.'_company');
        $overview->assertSee('/company/view/'.$this->companyId, false);

        $detail = $this->get('/company/view/'.$this->companyId);

        $detail->assertOk();
        $detail->assertSee($this->prefix.'_description');
        $detail->assertSee($this->prefix.'_character');
        $detail->assertSee($this->prefix.'_worker');
        $detail->assertSee($this->prefix.'_labour');
        $detail->assertSee('images/company-worker/3.png', false);
        $detail->assertDontSee('images/item/1.png', false);

        $this->actingAs(User::findOrFail($this->userId))
            ->get('/company/view/'.$this->companyId)
            ->assertOk()
            ->assertSee('Produktionsgut')
            ->assertSee('images/item/1.png', false);
    }

    public function test_company_worker_page_renders_read_only_labour_information(): void
    {
        $response = $this->get('/company/worker/'.$this->workerId);

        $response->assertOk();
        $response->assertSee($this->prefix.'_worker');
        $response->assertSee($this->prefix.'_company');
        $response->assertSee($this->prefix.'_labour');
        $response->assertSee('Zuweisung');
        $response->assertSee('Produktionsgut');
    }

    public function test_company_management_links_are_visible_only_to_owner(): void
    {
        $guest = $this->get('/company/view/'.$this->companyId);
        $guest->assertOk();
        $guest->assertDontSee('/company/fire/'.$this->workerId, false);
        $guest->assertDontSee('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/hire', false);

        $this->actingAs(User::findOrFail($this->otherUserId));
        $visitor = $this->get('/company/view/'.$this->companyId);
        $visitor->assertOk();
        $visitor->assertDontSee('/company/fire/'.$this->workerId, false);
        $visitor->assertDontSee('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/hire', false);

        $this->actingAs(User::findOrFail($this->userId));
        $owner = $this->get('/company/view/'.$this->companyId);
        $owner->assertOk();
        $owner->assertDontSee('/company/fire/'.$this->workerId, false);
        $owner->assertSee('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/hire/3', false);
    }

    public function test_company_worker_management_links_are_visible_only_to_owner(): void
    {
        $guest = $this->get('/company/worker/'.$this->workerId);
        $guest->assertOk();
        $guest->assertDontSee('/company/fire/'.$this->workerId, false);

        $this->actingAs(User::findOrFail($this->userId));
        $owner = $this->get('/company/worker/'.$this->workerId);
        $owner->assertOk();
        $owner->assertDontSee('/company/fire/'.$this->workerId, false);
        $owner->assertSee('/company/labour/', false);
    }

    public function test_company_owner_can_hire_worker_from_legacy_route(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $response = $this->get('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/hire/4');

        $response->assertRedirect('/company/view/'.$this->companyId);
        $this->assertDatabaseHas('company_workers', [
            'company_id' => $this->companyId,
            'type' => 4,
        ]);

        $detail = $this->followingRedirects()->get('/company/view/'.$this->companyId);
        $detail->assertOk();
        $detail->assertSee('Es wurde ein neuer Arbeiter namens');
    }

    public function test_non_owner_cannot_hire_worker(): void
    {
        $this->actingAs(User::findOrFail($this->otherUserId));

        $response = $this->get('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/hire/4');

        $response->assertForbidden();
        $this->assertSame(1, DB::table('company_workers')->where('company_id', $this->companyId)->count());
    }

    public function test_company_can_only_have_one_clerk(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        DB::table('company_workers')->insert([
            'name' => $this->prefix.'_clerk',
            'type' => 5,
            'company_id' => $this->companyId,
            'company_site_id' => $this->companySiteId,
            'hired' => $this->timestamp,
            'paid' => $this->timestamp,
        ]);

        $response = $this->get('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/hire/5');

        $response->assertRedirect('/company/view/'.$this->companyId);
        $this->assertSame(1, DB::table('company_workers')->where('company_id', $this->companyId)->where('type', 5)->count());

        $detail = $this->followingRedirects()->get('/company/view/'.$this->companyId);
        $detail->assertOk();
        $detail->assertSee('Dieser Betrieb verfügt bereits über einen Schreiber.');
    }

    public function test_company_owner_can_fire_worker_and_settle_unpaid_salary(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        DB::table('labour_actives')->where('company_worker_id', $this->workerId)->delete();

        DB::table('company_workers')
            ->where('id', $this->workerId)
            ->update(['paid' => now()->timestamp - (3 * 2592000)]);

        DB::table('inventories')->insert([
            'item_id' => 1,
            'stack' => 5 * Currency::TEN_PER_TUK,
            'wear' => -1,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $response = $this->get('/company/fire/'.$this->workerId);

        $response->assertRedirect('/company/view/'.$this->companyId);
        $this->assertDatabaseMissing('company_workers', ['id' => $this->workerId]);
        $this->assertDatabaseMissing('inventories', [
            'item_id' => 1,
            'wear' => -1,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);

        $detail = $this->followingRedirects()->get('/company/view/'.$this->companyId);
        $detail->assertOk();
        $detail->assertSee('Arbeiter');
        $detail->assertSee('erfolgreich entlassen');
        $detail->assertSee('5 tk ausbezahlt');
        $detail->assertSee('7 tk konnten nicht mehr ausbezahlt');
    }

    public function test_non_owner_cannot_fire_worker(): void
    {
        $this->actingAs(User::findOrFail($this->otherUserId));

        $response = $this->get('/company/fire/'.$this->workerId);

        $response->assertForbidden();
        $this->assertDatabaseHas('company_workers', ['id' => $this->workerId]);
        $this->assertDatabaseHas('labour_actives', ['company_worker_id' => $this->workerId]);
    }

    public function test_busy_worker_must_finish_work_before_being_fired(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $response = $this->get('/company/fire/'.$this->workerId);

        $response->assertRedirect('/company/worker/'.$this->workerId);
        $this->assertDatabaseHas('company_workers', ['id' => $this->workerId]);

        $detail = $this->followingRedirects()->get('/company/worker/'.$this->workerId);
        $detail->assertOk();
        $detail->assertSee('noch beschäftigt');
    }

    public function test_company_owner_can_pay_due_worker_salaries(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $period = 2592000;
        $firstPaidAt = now()->timestamp - (2 * $period);
        $secondPaidAt = now()->timestamp - (3 * $period);

        DB::table('company_workers')
            ->where('id', $this->workerId)
            ->update(['paid' => $firstPaidAt]);

        $secondWorkerId = DB::table('company_workers')->insertGetId([
            'name' => $this->prefix.'_second_worker',
            'type' => 1,
            'company_id' => $this->companyId,
            'company_site_id' => $this->companySiteId,
            'hired' => $this->timestamp,
            'paid' => $secondPaidAt,
        ]);

        DB::table('inventories')->insert([
            'item_id' => 1,
            'stack' => 17 * Currency::TEN_PER_TUK,
            'wear' => -1,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $response = $this->get('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/pay');

        $response->assertRedirect('/company/view/'.$this->companyId);
        $this->assertDatabaseMissing('inventories', [
            'item_id' => 1,
            'wear' => -1,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
        $this->assertDatabaseHas('company_workers', [
            'id' => $this->workerId,
            'paid' => $firstPaidAt + (2 * $period),
        ]);
        $this->assertDatabaseHas('company_workers', [
            'id' => $secondWorkerId,
            'paid' => $secondPaidAt + (3 * $period),
        ]);

        $detail = $this->followingRedirects()->get('/company/view/'.$this->companyId);
        $detail->assertOk();
        $detail->assertSee('2 Arbeiter mit insgesamt 17 tk ausgezahlt');
    }

    public function test_company_pay_distributes_limited_money_by_oldest_salary_month(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $period = 2592000;
        $paidAt = now()->timestamp - (2 * $period);

        DB::table('company_workers')
            ->where('id', $this->workerId)
            ->update(['paid' => $paidAt]);

        $secondWorkerId = DB::table('company_workers')->insertGetId([
            'name' => $this->prefix.'_unpaid_worker',
            'type' => 1,
            'company_id' => $this->companyId,
            'company_site_id' => $this->companySiteId,
            'hired' => $this->timestamp,
            'paid' => $paidAt,
        ]);

        DB::table('inventories')->insert([
            'item_id' => 1,
            'stack' => 8 * Currency::TEN_PER_TUK,
            'wear' => -1,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $response = $this->get('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/pay');

        $response->assertRedirect('/company/view/'.$this->companyId);
        $this->assertDatabaseHas('inventories', [
            'item_id' => 1,
            'stack' => Currency::TEN_PER_TUK,
            'wear' => -1,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
        $this->assertDatabaseHas('company_workers', [
            'id' => $this->workerId,
            'paid' => $paidAt + $period,
        ]);
        $this->assertDatabaseHas('company_workers', [
            'id' => $secondWorkerId,
            'paid' => $paidAt + $period,
        ]);

        $detail = $this->followingRedirects()->get('/company/view/'.$this->companyId);
        $detail->assertOk();
        $detail->assertSee('2 Arbeiter mit insgesamt 7 tk ausgezahlt (2 Monatslöhne)');
        $detail->assertSee('2 Arbeiter haben weiterhin ausstehenden Lohn');
    }

    public function test_company_pay_requires_owner_and_money(): void
    {
        $this->actingAs(User::findOrFail($this->otherUserId));

        $forbidden = $this->get('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/pay');
        $forbidden->assertForbidden();

        $this->actingAs(User::findOrFail($this->userId));
        $response = $this->get('/company/'.$this->companyId.'/sites/'.$this->companySiteId.'/pay');

        $response->assertRedirect('/company/view/'.$this->companyId);

        $detail = $this->followingRedirects()->get('/company/view/'.$this->companyId);
        $detail->assertOk();
        $detail->assertSee('Der Betrieb verfügt über keine Mittel');
    }

    public function test_worker_page_shows_assign_labour_form_for_possible_labours(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->prepareAssignableLabour();

        $response = $this->get('/company/worker/'.$this->workerId);

        $response->assertOk();
        $response->assertSee('Arbeit zuweisen');
        $response->assertSee($this->prefix.'_labour');
        $response->assertSee($this->prefix.'_item');
        $response->assertSee($this->prefix.'_tool');
        $response->assertSee($this->prefix.'_output');
        $response->assertSee('/company/worker/'.$this->workerId, false);
        $response->assertSee('name="prodas_value[til]"', false);
        $response->assertSee('name="prodas_value[tuk]"', false);
        $response->assertSee('name="prodas_value[ten]"', false);
    }

    public function test_owner_can_assign_labour_and_inventory_is_consumed_or_reserved(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->prepareAssignableLabour();
        DB::table('labour_actives')->where('company_worker_id', $this->workerId)->delete();
        DB::table('inventories')
            ->where('item_id', $this->itemId)
            ->where('owner_id', $this->companySiteId)
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->update(['stack' => 5]);
        DB::table('inventories')->insert([
            'item_id' => $this->toolItemId,
            'stack' => 0,
            'wear' => -2,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $response = $this->post('/company/worker/'.$this->workerId, [
            'labour' => $this->labourId,
            'quantity' => 0,
            'quantity_count' => 2,
            'instances' => 99,
            'prodas' => 0,
            'prodas_value' => [
                'til' => 0,
                'tuk' => Currency::TUK_PER_TIL,
                'ten' => Currency::TEN_PER_TUK + 1,
            ],
            'assignlabour' => 1,
        ]);

        $response->assertRedirect('/company/worker/'.$this->workerId);
        $this->assertDatabaseHas('labour_actives', [
            'company_worker_id' => $this->workerId,
            'labour_id' => $this->labourId,
            'prodas' => Currency::toTen(0, Currency::TUK_PER_TIL, Currency::TEN_PER_TUK + 1),
            'quantity' => 2,
            'instances' => 2,
            'nextinsta' => 0,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->itemId,
            'stack' => 1,
            'wear' => -2,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
        $this->assertSame(2, DB::table('inventories')
            ->where('item_id', $this->toolItemId)
            ->where('wear', -3)
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->where('owner_id', $this->companySiteId)
            ->count());
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->toolItemId,
            'wear' => -3,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
        $runId = (int) DB::table('production_runs')
            ->where('company_worker_id', $this->workerId)
            ->whereNull('completed_at')
            ->value('id');
        $this->assertGreaterThan(0, $runId);
        $run = DB::table('production_runs')->where('id', $runId)->first();
        $this->assertSame(4, (int) json_decode($run->inputs, true)[0]['quantity']);
        $this->assertSame(2, (int) json_decode($run->outputs, true)[0]['quantity']);
        $this->assertDatabaseHas('inventory_mutations', [
            'item_id' => $this->itemId,
            'kind' => 'consumption',
            'clock' => 'simulation',
            'source_type' => 'production_run',
            'source_id' => $runId,
        ]);
        $this->assertDatabaseHas('inventory_mutations', [
            'item_id' => $this->toolItemId,
            'kind' => 'state_change',
            'clock' => 'simulation',
            'source_type' => 'production_run',
            'source_id' => $runId,
        ]);

        $detail = $this->followingRedirects()->get('/company/worker/'.$this->workerId);
        $detail->assertOk();
        $detail->assertSee('Die Tätigkeit '.$this->prefix.'_labour wurde zugewiesen.');
    }

    public function test_non_owner_cannot_assign_labour(): void
    {
        $this->actingAs(User::findOrFail($this->otherUserId));
        $this->prepareAssignableLabour();

        $response = $this->post('/company/worker/'.$this->workerId, [
            'labour' => $this->labourId,
            'quantity' => 0,
            'quantity_count' => 1,
            'instances' => 1,
            'prodas' => -2,
            'assignlabour' => 1,
        ]);

        $response->assertForbidden();
        $this->assertSame(1, DB::table('labour_actives')->where('company_worker_id', $this->workerId)->count());
    }

    public function test_assign_labour_fails_when_required_inventory_is_missing(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->prepareAssignableLabour();
        DB::table('inventories')
            ->where('item_id', $this->itemId)
            ->where('owner_id', $this->companySiteId)
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->update(['stack' => 1]);

        $response = $this->post('/company/worker/'.$this->workerId, [
            'labour' => $this->labourId,
            'quantity' => 0,
            'quantity_count' => 1,
            'instances' => 1,
            'prodas' => -2,
            'assignlabour' => 1,
        ]);

        $response->assertRedirect('/company/worker/'.$this->workerId);
        $this->assertSame(1, DB::table('labour_actives')->where('company_worker_id', $this->workerId)->count());

        $detail = $this->followingRedirects()->get('/company/worker/'.$this->workerId);
        $detail->assertOk();
        $detail->assertSee('benötigten Rohstoffe');
    }

    public function test_labour_processor_finishes_job_creates_outputs_and_returns_tools(): void
    {
        $this->prepareAssignableLabour();
        $now = now()->timestamp;
        $completedAt = $now - 120;

        DB::table('company_workers')
            ->where('id', $this->workerId)
            ->update(['paid' => $now]);

        DB::table('labour_actives')
            ->where('company_worker_id', $this->workerId)
            ->update([
                'until' => $completedAt,
                'prodas' => -2,
                'quantity' => 1,
                'instances' => 1,
            ]);

        DB::table('inventories')
            ->where('item_id', $this->itemId)
            ->where('owner_id', $this->companySiteId)
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->update(['stack' => 1]);

        DB::table('inventories')
            ->where('item_id', $this->toolItemId)
            ->where('owner_id', $this->companySiteId)
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->update(['wear' => -3]);

        $activeLabourId = (int) DB::table('labour_actives')->where('company_worker_id', $this->workerId)->value('id');
        $stats = app(LabourProcessor::class)->processDue($now);

        $this->assertGreaterThanOrEqual(1, $stats['finished']);
        $this->assertDatabaseHas('labour_actives', [
            'id' => $activeLabourId,
            'quantity' => 0,
            'ended_at' => $completedAt,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->itemId,
            'stack' => 1,
            'wear' => -2,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->toolItemId,
            'wear' => -2,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 1,
            'wear' => -2,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
        $runId = (int) DB::table('production_runs')->where('labour_active_id', $activeLabourId)->value('id');
        $this->assertDatabaseHas('production_runs', [
            'id' => $runId,
            'company_id' => $this->companyId,
            'company_worker_id' => $this->workerId,
            'completed_at' => $completedAt,
        ]);
        $this->assertDatabaseHas('inventory_mutations', [
            'item_id' => $this->outputItemId,
            'kind' => 'production',
            'clock' => 'simulation',
            'effective_at' => $completedAt,
            'source_type' => 'production_run',
            'source_id' => $runId,
        ]);

        $detail = $this->get('/company/view/'.$this->companyId);
        $detail->assertOk();
        $detail->assertSee('Letzte Produktion');
        $detail->assertSee($this->prefix.'_output');
    }

    public function test_overdue_legacy_batch_finishes_once_and_ends_after_strike(): void
    {
        $this->prepareAssignableLabour();
        $now = now()->timestamp;

        DB::table('company_workers')
            ->where('id', $this->workerId)
            ->update(['paid' => $now - 7776001]);

        DB::table('labour_actives')
            ->where('company_worker_id', $this->workerId)
            ->update(['until' => $now - 120]);

        $this->get('/company/worker/'.$this->workerId)
            ->assertOk()
            ->assertSee('im Streik');

        $paidAt = $now - (77 * CompanyWorker::SALARY_PERIOD_SECONDS);
        $strikeStartedAt = $paidAt + (CompanyWorker::STRIKE_AFTER_PERIODS * CompanyWorker::SALARY_PERIOD_SECONDS);
        DB::table('company_workers')->where('id', $this->workerId)->update(['paid' => $paidAt]);
        DB::table('labour_actives')->where('company_worker_id', $this->workerId)->update([
            'since' => $strikeStartedAt - 240,
            'until' => $strikeStartedAt - 120,
        ]);

        $this->get('/company/worker/'.$this->workerId)
            ->assertOk()
            ->assertSee('im Streik (77 Monate ohne Gehalt)');

        $stats = app(LabourProcessor::class)->processDue($now);
        $completedAt = $strikeStartedAt - 120;

        $this->assertGreaterThanOrEqual(1, $stats['finished']);
        $this->assertDatabaseHas('labour_actives', [
            'company_worker_id' => $this->workerId,
            'quantity' => 0,
            'ended_at' => $completedAt,
            'paused_at' => null,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 1,
            'owner_id' => $this->companySiteId,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
        ]);
    }

    public function test_strike_pauses_running_batch_and_payment_resumes_order(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->prepareAssignableLabour();
        DB::table('labour_actives')->where('company_worker_id', $this->workerId)->delete();
        DB::table('company_workers')->where('id', $this->workerId)->update(['paid' => now()->timestamp]);
        DB::table('inventories')
            ->where('item_id', $this->itemId)
            ->where('owner_id', $this->companySiteId)
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->update(['stack' => 5]);

        $this->post('/company/worker/'.$this->workerId, [
            'labour' => $this->labourId,
            'quantity' => -1,
            'instances' => 1,
            'prodas' => -2,
            'assignlabour' => 1,
        ])->assertRedirect('/company/worker/'.$this->workerId);

        $activeLabourId = (int) DB::table('labour_actives')
            ->where('company_worker_id', $this->workerId)
            ->whereNull('ended_at')
            ->value('id');
        $runId = (int) DB::table('production_runs')->where('labour_active_id', $activeLabourId)->value('id');
        $originalDueAt = (int) DB::table('production_runs')->where('id', $runId)->value('due_at');
        $strikeAt = $originalDueAt - 60;

        DB::table('company_workers')->where('id', $this->workerId)->update([
            'paid' => $strikeAt - (CompanyWorker::STRIKE_AFTER_PERIODS * CompanyWorker::SALARY_PERIOD_SECONDS),
        ]);

        $stats = app(LabourProcessor::class)->processDue($strikeAt);

        $this->assertGreaterThanOrEqual(1, $stats['paused']);
        $this->assertDatabaseHas('labour_actives', [
            'id' => $activeLabourId,
            'paused_at' => $strikeAt,
            'pause_reason' => 'strike',
            'ended_at' => null,
        ]);
        $this->assertDatabaseHas('production_runs', [
            'id' => $runId,
            'due_at' => $originalDueAt,
            'completed_at' => null,
        ]);
        $this->get('/company/worker/'.$this->workerId)
            ->assertOk()
            ->assertSee('Pausiert seit:')
            ->assertSee('Wegen Streik pausiert')
            ->assertSee('nach der Lohnauszahlung fortgesetzt');

        $resumeAt = $strikeAt + 3600;
        DB::table('company_workers')->where('id', $this->workerId)->update(['paid' => $resumeAt]);

        app(LabourProcessor::class)->processDue($resumeAt);

        $rescheduledDueAt = $originalDueAt + 3600;
        $this->assertDatabaseHas('labour_actives', [
            'id' => $activeLabourId,
            'paused_at' => null,
            'pause_reason' => null,
            'until' => $rescheduledDueAt,
            'ended_at' => null,
        ]);
        $this->assertDatabaseHas('production_runs', [
            'id' => $runId,
            'due_at' => $rescheduledDueAt,
            'completed_at' => null,
        ]);
        $this->assertDatabaseMissing('inventories', [
            'item_id' => $this->outputItemId,
            'owner_id' => $this->companySiteId,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
        ]);

        app(LabourProcessor::class)->processDue($rescheduledDueAt);

        $this->assertDatabaseHas('production_runs', ['id' => $runId, 'completed_at' => $rescheduledDueAt]);
        $this->assertSame(2, DB::table('production_runs')->where('labour_active_id', $activeLabourId)->count());
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 1,
            'owner_id' => $this->companySiteId,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
        ]);
        $this->assertDatabaseHas('labour_actives', ['id' => $activeLabourId, 'ended_at' => null]);
    }

    public function test_repeating_work_consumes_each_batch_once_and_keeps_a_durable_run(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->prepareAssignableLabour();
        DB::table('labour_actives')->where('company_worker_id', $this->workerId)->delete();
        DB::table('company_workers')->where('id', $this->workerId)->update(['paid' => now()->timestamp]);
        DB::table('inventories')
            ->where('item_id', $this->itemId)
            ->where('owner_id', $this->companySiteId)
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->update(['stack' => 5]);

        $this->post('/company/worker/'.$this->workerId, [
            'labour' => $this->labourId,
            'quantity' => 0,
            'quantity_count' => 2,
            'instances' => 1,
            'prodas' => -2,
            'assignlabour' => 1,
        ])->assertRedirect('/company/worker/'.$this->workerId);

        $activeLabourId = (int) DB::table('labour_actives')
            ->where('company_worker_id', $this->workerId)
            ->whereNull('ended_at')
            ->value('id');
        $firstRunId = (int) DB::table('production_runs')->where('labour_active_id', $activeLabourId)->value('id');
        $firstCompletion = now()->addMinutes(5)->timestamp;
        DB::table('production_runs')->where('id', $firstRunId)->update(['due_at' => $firstCompletion - 1]);
        DB::table('labour_actives')->where('id', $activeLabourId)->update(['until' => $firstCompletion - 1]);

        app(LabourProcessor::class)->processDue($firstCompletion);

        $this->assertDatabaseHas('production_runs', ['id' => $firstRunId, 'completed_at' => $firstCompletion - 1]);
        $this->assertSame(2, DB::table('production_runs')->where('labour_active_id', $activeLabourId)->count());
        $this->assertDatabaseHas('labour_actives', ['id' => $activeLabourId, 'quantity' => 1, 'ended_at' => null]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->itemId,
            'stack' => 1,
            'wear' => -2,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 1,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);

        $secondRunId = (int) DB::table('production_runs')
            ->where('labour_active_id', $activeLabourId)
            ->whereNull('completed_at')
            ->value('id');
        $secondCompletion = $firstCompletion + 300;
        DB::table('production_runs')->where('id', $secondRunId)->update(['due_at' => $secondCompletion - 1]);
        DB::table('labour_actives')->where('id', $activeLabourId)->update(['until' => $secondCompletion - 1]);

        app(LabourProcessor::class)->processDue($secondCompletion);

        $this->assertDatabaseHas('labour_actives', ['id' => $activeLabourId, 'quantity' => 0, 'ended_at' => $secondCompletion - 1]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 2,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->toolItemId,
            'wear' => -2,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
    }

    public function test_processor_catches_up_every_elapsed_production_run_in_chronological_order(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->prepareAssignableLabour();
        DB::table('labour_actives')->where('company_worker_id', $this->workerId)->delete();
        DB::table('company_workers')->where('id', $this->workerId)->update(['paid' => now()->timestamp]);
        DB::table('inventories')
            ->where('item_id', $this->itemId)
            ->where('owner_id', $this->companySiteId)
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->update(['stack' => 20]);

        $this->post('/company/worker/'.$this->workerId, [
            'labour' => $this->labourId,
            'quantity' => 0,
            'quantity_count' => 3,
            'instances' => 1,
            'prodas' => -2,
            'assignlabour' => 1,
        ])->assertRedirect('/company/worker/'.$this->workerId);

        $now = now()->timestamp;
        $duration = max(1, (int) DB::table('labours')->where('id', $this->labourId)->value('duration'));
        $firstDueAt = $now - (2 * $duration);
        $activeLabourId = (int) DB::table('labour_actives')
            ->where('company_worker_id', $this->workerId)
            ->whereNull('ended_at')
            ->value('id');
        $firstRunId = (int) DB::table('production_runs')->where('labour_active_id', $activeLabourId)->value('id');

        DB::table('production_runs')->where('id', $firstRunId)->update([
            'started_at' => $firstDueAt - $duration,
            'due_at' => $firstDueAt,
        ]);
        DB::table('labour_actives')->where('id', $activeLabourId)->update([
            'since' => $firstDueAt - $duration,
            'until' => $firstDueAt,
        ]);

        $stats = app(LabourProcessor::class)->processDue($now);

        $runs = DB::table('production_runs')
            ->where('labour_active_id', $activeLabourId)
            ->orderBy('id')
            ->get();

        $this->assertCount(3, $runs);
        $this->assertSame(
            [$firstDueAt - $duration, $firstDueAt, $firstDueAt + $duration],
            $runs->pluck('started_at')->map(fn ($timestamp) => (int) $timestamp)->all(),
        );
        $this->assertSame(
            [$firstDueAt, $firstDueAt + $duration, $now],
            $runs->pluck('completed_at')->map(fn ($timestamp) => (int) $timestamp)->all(),
        );
        $this->assertSame(1, $stats['finished']);
        $this->assertFalse($stats['limit_reached']);
        $this->assertDatabaseHas('labour_actives', [
            'id' => $activeLabourId,
            'quantity' => 0,
            'ended_at' => $now,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 3,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
    }

    public function test_catch_up_finishes_cycles_due_before_a_strike_and_then_pauses(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->prepareAssignableLabour();
        DB::table('labour_actives')->where('company_worker_id', $this->workerId)->delete();
        DB::table('inventories')
            ->where('item_id', $this->itemId)
            ->where('owner_id', $this->companySiteId)
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->update(['stack' => 20]);

        $this->post('/company/worker/'.$this->workerId, [
            'labour' => $this->labourId,
            'quantity' => -1,
            'instances' => 1,
            'prodas' => -2,
            'assignlabour' => 1,
        ])->assertRedirect('/company/worker/'.$this->workerId);

        $now = now()->timestamp;
        $duration = max(1, (int) DB::table('labours')->where('id', $this->labourId)->value('duration'));
        $strikeStartedAt = $now - max(1, intdiv($duration, 2));
        $firstDueAt = $strikeStartedAt - (2 * $duration);
        $activeLabourId = (int) DB::table('labour_actives')
            ->where('company_worker_id', $this->workerId)
            ->whereNull('ended_at')
            ->value('id');
        $firstRunId = (int) DB::table('production_runs')->where('labour_active_id', $activeLabourId)->value('id');

        DB::table('company_workers')->where('id', $this->workerId)->update([
            'paid' => $strikeStartedAt - (CompanyWorker::STRIKE_AFTER_PERIODS * CompanyWorker::SALARY_PERIOD_SECONDS),
        ]);
        DB::table('production_runs')->where('id', $firstRunId)->update([
            'started_at' => $firstDueAt - $duration,
            'due_at' => $firstDueAt,
        ]);
        DB::table('labour_actives')->where('id', $activeLabourId)->update([
            'since' => $firstDueAt - $duration,
            'until' => $firstDueAt,
        ]);

        $stats = app(LabourProcessor::class)->processDue($now);
        $runs = DB::table('production_runs')
            ->where('labour_active_id', $activeLabourId)
            ->orderBy('id')
            ->get();

        $this->assertCount(4, $runs);
        $this->assertSame(
            [$firstDueAt, $firstDueAt + $duration, $strikeStartedAt],
            $runs->whereNotNull('completed_at')->pluck('completed_at')->map(fn ($timestamp) => (int) $timestamp)->all(),
        );
        $this->assertSame(1, $stats['paused']);
        $this->assertDatabaseHas('labour_actives', [
            'id' => $activeLabourId,
            'paused_at' => $strikeStartedAt,
            'pause_reason' => 'strike',
            'ended_at' => null,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 3,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
    }

    public function test_owner_can_stop_after_current_batch_and_tools_are_returned(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->prepareAssignableLabour();
        DB::table('labour_actives')->where('company_worker_id', $this->workerId)->delete();
        DB::table('company_workers')->where('id', $this->workerId)->update(['paid' => now()->timestamp]);

        $this->post('/company/worker/'.$this->workerId, [
            'labour' => $this->labourId,
            'quantity' => -1,
            'instances' => 1,
            'prodas' => -2,
            'assignlabour' => 1,
        ])->assertRedirect('/company/worker/'.$this->workerId);

        $activeLabourId = (int) DB::table('labour_actives')
            ->where('company_worker_id', $this->workerId)
            ->whereNull('ended_at')
            ->value('id');
        $runId = (int) DB::table('production_runs')->where('labour_active_id', $activeLabourId)->value('id');

        $response = $this->post('/company/labour/'.$activeLabourId.'/stop');

        $response->assertRedirect('/company/worker/'.$this->workerId);
        $this->assertDatabaseHas('labour_actives', [
            'id' => $activeLabourId,
            'ended_at' => null,
        ]);
        $this->assertNotNull(DB::table('labour_actives')->where('id', $activeLabourId)->value('stop_requested_at'));
        $detail = $this->followingRedirects()->get('/company/worker/'.$this->workerId);
        $detail->assertOk();
        $detail->assertSee('nach dem laufenden Durchgang beendet');

        $now = now()->addMinutes(5)->timestamp;
        DB::table('production_runs')->where('id', $runId)->update(['due_at' => $now - 1]);
        DB::table('labour_actives')->where('id', $activeLabourId)->update(['until' => $now - 1]);
        $stats = app(LabourProcessor::class)->processDue($now);

        $this->assertGreaterThanOrEqual(1, $stats['finished']);
        $this->assertDatabaseHas('labour_actives', ['id' => $activeLabourId, 'ended_at' => $now - 1]);
        $this->assertDatabaseHas('production_runs', ['id' => $runId, 'completed_at' => $now - 1]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->toolItemId,
            'wear' => -2,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 1,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
        ]);
        $toolInventoryId = (int) DB::table('inventories')
            ->where('item_id', $this->toolItemId)
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->where('owner_id', $this->companySiteId)
            ->value('id');
        $outputInventoryId = (int) DB::table('inventories')
            ->where('item_id', $this->outputItemId)
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->where('owner_id', $this->companySiteId)
            ->value('id');
        $this->artisan('economy:audit-inventory --inventory='.$toolInventoryId)
            ->expectsOutputToContain('Issues found: 0')
            ->assertExitCode(0);
        $this->artisan('economy:audit-inventory --inventory='.$outputInventoryId)
            ->expectsOutputToContain('Issues found: 0')
            ->assertExitCode(0);
    }

    public function test_non_owner_cannot_stop_company_work(): void
    {
        $this->actingAs(User::findOrFail($this->otherUserId));
        $activeLabourId = (int) DB::table('labour_actives')->where('company_worker_id', $this->workerId)->value('id');

        $this->post('/company/labour/'.$activeLabourId.'/stop')->assertForbidden();

        $this->assertDatabaseHas('labour_actives', [
            'id' => $activeLabourId,
            'stop_requested_at' => null,
            'ended_at' => null,
        ]);
    }

    private function prepareAssignableLabour(): void
    {
        DB::table('labour_components')->insert([
            [
                'labour_id' => $this->labourId,
                'item_id' => $this->itemId,
                'quantity' => 2,
                'type' => 0,
            ],
            [
                'labour_id' => $this->labourId,
                'item_id' => $this->toolItemId,
                'quantity' => 1,
                'type' => 1,
            ],
            [
                'labour_id' => $this->labourId,
                'item_id' => $this->outputItemId,
                'quantity' => 1,
                'type' => 2,
            ],
        ]);

        DB::table('inventories')->insert([
            'item_id' => $this->toolItemId,
            'stack' => 0,
            'wear' => -2,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'owner_id' => $this->companySiteId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);
    }

    private function createCharacter(int $userId, string $suffix): int
    {
        return DB::table('characters')->insertGetId([
            'name' => $this->prefix.$suffix,
            'post_count' => 0,
            'regdate' => $this->timestamp,
            'birthday' => 0,
            'interests' => '',
            'location' => '',
            'work' => '',
            'gender' => 0,
            'usertext' => '',
            'user_id' => $userId,
        ]);
    }
}
