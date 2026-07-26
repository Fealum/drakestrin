<?php

namespace Tests\Feature;

use App\Models\Economy\CompanyWorker;
use App\Models\User;
use App\Services\Economy\LabourProcessor;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CompanyReadTest extends TestCase
{
    private string $prefix;

    private int $userId;

    private int $otherUserId;

    private int $characterId;

    private int $companyId;

    private int $workerId;

    private int $itemId;

    private int $toolItemId;

    private int $outputItemId;

    private int $labourId;

    private int $timestamp;

    protected function setUp(): void
    {
        parent::setUp();

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

        $this->companyId = DB::table('companies')->insertGetId([
            'name' => $this->prefix.'_company',
            'type' => 2,
            'character_id' => $this->characterId,
            'description' => $this->prefix.'_description',
            'text' => '',
            'territory_id' => $territoryId,
            'thread_id' => 0,
            'url' => '',
            'volksgeld' => 0,
        ]);

        $this->workerId = DB::table('company_workers')->insertGetId([
            'name' => $this->prefix.'_worker',
            'type' => 3,
            'company_id' => $this->companyId,
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
            'owner_type' => 2,
            'owner_id' => $this->companyId,
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
        DB::table('company_sites')->whereIn('company_id', $companyIds)->delete();
        DB::table('inventory_mutations')->whereIn('item_id', [$this->itemId, $this->toolItemId, $this->outputItemId])->delete();
        DB::table('production_runs')->where('company_id', $this->companyId)->delete();
        DB::table('labour_actives')->where('company_worker_id', $this->workerId)->delete();
        DB::table('labour_components')
            ->where('labour_id', $this->labourId)
            ->orWhereIn('item_id', [$this->itemId, $this->toolItemId, $this->outputItemId])
            ->delete();
        DB::table('labours')->where('id', $this->labourId)->delete();
        DB::table('inventories')->whereIn('owner_id', $companyIds)->where('owner_type', 2)->delete();
        DB::table('items')->whereIn('id', [$this->itemId, $this->toolItemId, $this->outputItemId])->delete();
        DB::table('company_workers')->where('company_id', $this->companyId)->delete();
        DB::table('companies')->whereIn('id', $companyIds)->delete();
        DB::table('characters')->where('name', 'like', $this->prefix.'%')->delete();
        DB::table('onlines')->whereIn('user_id', [$this->userId, $this->otherUserId])->delete();
        DB::table('users')->whereIn('id', [$this->userId, $this->otherUserId])->delete();

        parent::tearDown();
    }

    public function test_owner_can_create_and_edit_company_at_location(): void
    {
        $locationId = (int) DB::table('locations')->value('id');
        $this->assertGreaterThan(0, $locationId);

        $this->actingAs(User::findOrFail($this->userId))
            ->get('/company/create')
            ->assertOk()
            ->assertSee('Betrieb gründen');

        $this->post('/company', [
            'name' => $this->prefix.'_new_company',
            'sector' => 3,
            'owner_character_id' => $this->characterId,
            'location_id' => $locationId,
            'description' => 'Kurz',
            'text' => 'Lang',
            'url' => 'https://example.test/company',
            'is_storefront' => 1,
        ])
            ->assertRedirect();

        $companyId = (int) DB::table('companies')
            ->where('name', $this->prefix.'_new_company')
            ->value('id');

        $this->assertDatabaseHas('company_sites', [
            'company_id' => $companyId,
            'location_id' => $locationId,
            'is_headquarters' => 1,
            'is_storefront' => 1,
        ]);

        $this->get('/company/edit/'.$companyId)
            ->assertOk()
            ->assertSee('Betrieb bearbeiten');

        $this->put('/company/edit/'.$companyId, [
            'name' => $this->prefix.'_renamed_company',
            'sector' => 4,
            'owner_character_id' => $this->characterId,
            'location_id' => $locationId,
            'description' => 'Neu',
            'text' => 'Neuer Text',
            'url' => '',
            'is_storefront' => 0,
        ])->assertRedirect('/company/view/'.$companyId);

        $this->assertDatabaseHas('companies', [
            'id' => $companyId,
            'name' => $this->prefix.'_renamed_company',
            'type' => 4,
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
            ->assertSee('Neuen Handwerker einstellen');
    }

    public function test_manager_can_reclassify_part_of_company_stock_for_sale(): void
    {
        $inventoryId = (int) DB::table('inventories')
            ->where('owner_type', 2)
            ->where('owner_id', $this->companyId)
            ->where('item_id', $this->itemId)
            ->value('id');

        $this->actingAs(User::findOrFail($this->userId))
            ->put('/company/'.$this->companyId.'/inventory/'.$inventoryId, [
                'state' => 'sale',
                'price' => '2,5',
                'quantity' => '1',
            ])
            ->assertRedirect('/company/view/'.$this->companyId);

        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->itemId,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
            'wear' => 25000,
            'stack' => 1,
        ]);
        $this->assertDatabaseHas('inventories', [
            'id' => $inventoryId,
            'wear' => -2,
            'stack' => 2,
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
        $detail->assertSee('Produktionsgut');
        $detail->assertSee('images/company-worker/3.png', false);
        $detail->assertSee('images/item/1.png', false);
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
        $guest->assertDontSee('/company/hire/'.$this->companyId, false);

        $this->actingAs(User::findOrFail($this->otherUserId));
        $visitor = $this->get('/company/view/'.$this->companyId);
        $visitor->assertOk();
        $visitor->assertDontSee('/company/fire/'.$this->workerId, false);
        $visitor->assertDontSee('/company/hire/'.$this->companyId, false);

        $this->actingAs(User::findOrFail($this->userId));
        $owner = $this->get('/company/view/'.$this->companyId);
        $owner->assertOk();
        $owner->assertDontSee('/company/fire/'.$this->workerId, false);
        $owner->assertSee('/company/hire/'.$this->companyId.'/3', false);
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

        $response = $this->get('/company/hire/'.$this->companyId.'/4');

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

        $response = $this->get('/company/hire/'.$this->companyId.'/4');

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
            'hired' => $this->timestamp,
            'paid' => $this->timestamp,
        ]);

        $response = $this->get('/company/hire/'.$this->companyId.'/5');

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
            'stack' => 50000,
            'wear' => -1,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $response = $this->get('/company/fire/'.$this->workerId);

        $response->assertRedirect('/company/view/'.$this->companyId);
        $this->assertDatabaseMissing('company_workers', ['id' => $this->workerId]);
        $this->assertDatabaseMissing('inventories', [
            'item_id' => 1,
            'wear' => -1,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
        ]);

        $detail = $this->followingRedirects()->get('/company/view/'.$this->companyId);
        $detail->assertOk();
        $detail->assertSee('Arbeiter');
        $detail->assertSee('erfolgreich entlassen');
        $detail->assertSee('5,00 Tuk ausbezahlt');
        $detail->assertSee('7,00 Tuk konnten nicht mehr ausbezahlt');
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
            'hired' => $this->timestamp,
            'paid' => $secondPaidAt,
        ]);

        DB::table('inventories')->insert([
            'item_id' => 1,
            'stack' => 170000,
            'wear' => -1,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $response = $this->get('/company/pay/'.$this->companyId);

        $response->assertRedirect('/company/view/'.$this->companyId);
        $this->assertDatabaseMissing('inventories', [
            'item_id' => 1,
            'wear' => -1,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
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
        $detail->assertSee('2 Arbeiter mit insgesamt 17,00 Tuk ausgezahlt');
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
            'hired' => $this->timestamp,
            'paid' => $paidAt,
        ]);

        DB::table('inventories')->insert([
            'item_id' => 1,
            'stack' => 80000,
            'wear' => -1,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $response = $this->get('/company/pay/'.$this->companyId);

        $response->assertRedirect('/company/view/'.$this->companyId);
        $this->assertDatabaseHas('inventories', [
            'item_id' => 1,
            'stack' => 10000,
            'wear' => -1,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
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
        $detail->assertSee('2 Arbeiter mit insgesamt 7,00 Tuk ausgezahlt (2 Monatslöhne)');
        $detail->assertSee('2 Arbeiter haben weiterhin ausstehenden Lohn');
    }

    public function test_company_pay_requires_owner_and_money(): void
    {
        $this->actingAs(User::findOrFail($this->otherUserId));

        $forbidden = $this->get('/company/pay/'.$this->companyId);
        $forbidden->assertForbidden();

        $this->actingAs(User::findOrFail($this->userId));
        $response = $this->get('/company/pay/'.$this->companyId);

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
    }

    public function test_owner_can_assign_labour_and_inventory_is_consumed_or_reserved(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->prepareAssignableLabour();
        DB::table('labour_actives')->where('company_worker_id', $this->workerId)->delete();
        DB::table('inventories')
            ->where('item_id', $this->itemId)
            ->where('owner_id', $this->companyId)
            ->where('owner_type', 2)
            ->update(['stack' => 5]);
        DB::table('inventories')->insert([
            'item_id' => $this->toolItemId,
            'stack' => 0,
            'wear' => -2,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $response = $this->post('/company/worker/'.$this->workerId, [
            'labour' => $this->labourId,
            'quantity' => 0,
            'quantity_count' => 2,
            'instances' => 99,
            'prodas' => 0,
            'prodas_value' => 25,
            'assignlabour' => 1,
        ]);

        $response->assertRedirect('/company/worker/'.$this->workerId);
        $this->assertDatabaseHas('labour_actives', [
            'company_worker_id' => $this->workerId,
            'labour_id' => $this->labourId,
            'prodas' => 25,
            'quantity' => 2,
            'instances' => 2,
            'nextinsta' => 0,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->itemId,
            'stack' => 1,
            'wear' => -2,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
        ]);
        $this->assertSame(2, DB::table('inventories')
            ->where('item_id', $this->toolItemId)
            ->where('wear', -3)
            ->where('owner_type', 2)
            ->where('owner_id', $this->companyId)
            ->count());
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->toolItemId,
            'wear' => -3,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
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
            ->where('owner_id', $this->companyId)
            ->where('owner_type', 2)
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

        DB::table('company_workers')
            ->where('id', $this->workerId)
            ->update(['paid' => $now]);

        DB::table('labour_actives')
            ->where('company_worker_id', $this->workerId)
            ->update([
                'until' => $now - 120,
                'prodas' => -2,
                'quantity' => 1,
                'instances' => 1,
            ]);

        DB::table('inventories')
            ->where('item_id', $this->itemId)
            ->where('owner_id', $this->companyId)
            ->where('owner_type', 2)
            ->update(['stack' => 1]);

        DB::table('inventories')
            ->where('item_id', $this->toolItemId)
            ->where('owner_id', $this->companyId)
            ->where('owner_type', 2)
            ->update(['wear' => -3]);

        $activeLabourId = (int) DB::table('labour_actives')->where('company_worker_id', $this->workerId)->value('id');
        $stats = app(LabourProcessor::class)->processDue($now);

        $this->assertGreaterThanOrEqual(1, $stats['finished']);
        $this->assertDatabaseHas('labour_actives', [
            'id' => $activeLabourId,
            'quantity' => 0,
            'ended_at' => $now,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->itemId,
            'stack' => 1,
            'wear' => -2,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->toolItemId,
            'wear' => -2,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 1,
            'wear' => -2,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
        ]);
        $runId = (int) DB::table('production_runs')->where('labour_active_id', $activeLabourId)->value('id');
        $this->assertDatabaseHas('production_runs', [
            'id' => $runId,
            'company_id' => $this->companyId,
            'company_worker_id' => $this->workerId,
            'completed_at' => $now,
        ]);
        $this->assertDatabaseHas('inventory_mutations', [
            'item_id' => $this->outputItemId,
            'kind' => 'production',
            'clock' => 'simulation',
            'effective_at' => $now,
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

        $this->assertGreaterThanOrEqual(1, $stats['finished']);
        $this->assertDatabaseHas('labour_actives', [
            'company_worker_id' => $this->workerId,
            'quantity' => 0,
            'ended_at' => $now,
            'paused_at' => null,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 1,
            'owner_id' => $this->companyId,
            'owner_type' => 2,
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
            ->where('owner_id', $this->companyId)
            ->where('owner_type', 2)
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
            'owner_id' => $this->companyId,
            'owner_type' => 2,
        ]);

        app(LabourProcessor::class)->processDue($rescheduledDueAt);

        $this->assertDatabaseHas('production_runs', ['id' => $runId, 'completed_at' => $rescheduledDueAt]);
        $this->assertSame(2, DB::table('production_runs')->where('labour_active_id', $activeLabourId)->count());
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 1,
            'owner_id' => $this->companyId,
            'owner_type' => 2,
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
            ->where('owner_id', $this->companyId)
            ->where('owner_type', 2)
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

        $this->assertDatabaseHas('production_runs', ['id' => $firstRunId, 'completed_at' => $firstCompletion]);
        $this->assertSame(2, DB::table('production_runs')->where('labour_active_id', $activeLabourId)->count());
        $this->assertDatabaseHas('labour_actives', ['id' => $activeLabourId, 'quantity' => 1, 'ended_at' => null]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->itemId,
            'stack' => 1,
            'wear' => -2,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 1,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
        ]);

        $secondRunId = (int) DB::table('production_runs')
            ->where('labour_active_id', $activeLabourId)
            ->whereNull('completed_at')
            ->value('id');
        $secondCompletion = $firstCompletion + 300;
        DB::table('production_runs')->where('id', $secondRunId)->update(['due_at' => $secondCompletion - 1]);
        DB::table('labour_actives')->where('id', $activeLabourId)->update(['until' => $secondCompletion - 1]);

        app(LabourProcessor::class)->processDue($secondCompletion);

        $this->assertDatabaseHas('labour_actives', ['id' => $activeLabourId, 'quantity' => 0, 'ended_at' => $secondCompletion]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 2,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->toolItemId,
            'wear' => -2,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
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
        $this->assertDatabaseHas('labour_actives', ['id' => $activeLabourId, 'ended_at' => $now]);
        $this->assertDatabaseHas('production_runs', ['id' => $runId, 'completed_at' => $now]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->toolItemId,
            'wear' => -2,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $this->outputItemId,
            'stack' => 1,
            'owner_type' => 2,
            'owner_id' => $this->companyId,
        ]);
        $toolInventoryId = (int) DB::table('inventories')
            ->where('item_id', $this->toolItemId)
            ->where('owner_type', 2)
            ->where('owner_id', $this->companyId)
            ->value('id');
        $outputInventoryId = (int) DB::table('inventories')
            ->where('item_id', $this->outputItemId)
            ->where('owner_type', 2)
            ->where('owner_id', $this->companyId)
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
            'owner_type' => 2,
            'owner_id' => $this->companyId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);
    }
}
