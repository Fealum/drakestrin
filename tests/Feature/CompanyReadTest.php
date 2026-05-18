<?php

namespace Tests\Feature;

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

        $this->prefix = '000_ct_company_' . substr(str_replace('.', '_', uniqid('', true)), 0, 8);
        $this->timestamp = now()->subDays(10)->timestamp;

        $this->userId = DB::table('dra_user')->insertGetId([
            'name' => $this->prefix . '_owner',
            'password' => 'secret',
            'email' => $this->prefix . '_owner@example.test',
            'regemail' => $this->prefix . '_owner@example.test',
            'regdate' => $this->timestamp,
            'lastvisit' => $this->timestamp,
            'lastactivity' => $this->timestamp,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'wohnort' => '',
        ]);

        $this->otherUserId = DB::table('dra_user')->insertGetId([
            'name' => $this->prefix . '_visitor',
            'password' => 'secret',
            'email' => $this->prefix . '_visitor@example.test',
            'regemail' => $this->prefix . '_visitor@example.test',
            'regdate' => $this->timestamp,
            'lastvisit' => $this->timestamp,
            'lastactivity' => $this->timestamp,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'wohnort' => '',
        ]);

        $this->characterId = DB::table('dra_character')->insertGetId([
            'name' => $this->prefix . '_character',
            'post__total' => 0,
            'regdate' => $this->timestamp,
            'birthday' => 0,
            'interests' => '',
            'location' => '',
            'work' => '',
            'gender' => 0,
            'usertext' => '',
            'user' => $this->userId,
        ]);

        $territoryId = (int) DB::table('dra_territory')->value('id');

        $this->companyId = DB::table('dra_company')->insertGetId([
            'name' => $this->prefix . '_company',
            'type' => 2,
            'character' => $this->characterId,
            'description' => $this->prefix . '_description',
            'text' => '',
            'territory' => $territoryId,
            'thread' => 0,
            'url' => '',
            'volksgeld' => 0,
        ]);

        $this->workerId = DB::table('dra_company_worker')->insertGetId([
            'name' => $this->prefix . '_worker',
            'type' => 3,
            'company' => $this->companyId,
            'hired' => $this->timestamp,
            'paid' => $this->timestamp,
        ]);

        $this->itemId = DB::table('dra_item')->insertGetId([
            'name' => $this->prefix . '_item',
            'stackable' => 1,
            'description' => '',
            'img' => 1,
            'weight' => 1,
            'unit' => '',
        ]);

        $this->toolItemId = DB::table('dra_item')->insertGetId([
            'name' => $this->prefix . '_tool',
            'stackable' => 0,
            'description' => '',
            'img' => 1,
            'weight' => 1,
            'unit' => '',
        ]);

        $this->outputItemId = DB::table('dra_item')->insertGetId([
            'name' => $this->prefix . '_output',
            'stackable' => 1,
            'description' => '',
            'img' => 1,
            'weight' => 1,
            'unit' => '',
        ]);

        DB::table('dra_inventory')->insert([
            'item' => $this->itemId,
            'stack' => 3,
            'wear' => -2,
            'table__owner' => 2,
            'owner' => $this->companyId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $this->labourId = DB::table('dra_labour')->insertGetId([
            'name' => $this->prefix . '_labour',
            'type' => 2,
            'duration' => 120,
            'workload' => 2,
            'benwerkzeug' => '',
            'benrohstoff' => '',
            'ergebnis' => '',
        ]);

        DB::table('dra_labour_active')->insert([
            'company_worker' => $this->workerId,
            'labour' => $this->labourId,
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
        DB::table('dra_labour_active')->where('company_worker', $this->workerId)->delete();
        DB::table('dra_labour_component')
            ->where('labour', $this->labourId)
            ->orWhereIn('item', [$this->itemId, $this->toolItemId, $this->outputItemId])
            ->delete();
        DB::table('dra_labour')->where('id', $this->labourId)->delete();
        DB::table('dra_inventory')->where('owner', $this->companyId)->where('table__owner', 2)->delete();
        DB::table('dra_item')->whereIn('id', [$this->itemId, $this->toolItemId, $this->outputItemId])->delete();
        DB::table('dra_company_worker')->where('company', $this->companyId)->delete();
        DB::table('dra_company')->where('id', $this->companyId)->delete();
        DB::table('dra_character')->where('id', $this->characterId)->delete();
        DB::table('dra_online')->whereIn('user', [$this->userId, $this->otherUserId])->delete();
        DB::table('dra_user')->whereIn('id', [$this->userId, $this->otherUserId])->delete();

        parent::tearDown();
    }

    public function test_company_overview_and_detail_render_legacy_shape(): void
    {
        $overview = $this->get('/company');

        $overview->assertOk();
        $overview->assertSee('Kontor');
        $overview->assertSee($this->prefix . '_company');
        $overview->assertSee('/company/view/' . $this->companyId, false);

        $detail = $this->get('/company/view/' . $this->companyId);

        $detail->assertOk();
        $detail->assertSee($this->prefix . '_description');
        $detail->assertSee($this->prefix . '_character');
        $detail->assertSee($this->prefix . '_worker');
        $detail->assertSee($this->prefix . '_labour');
        $detail->assertSee('Produktionsgut');
        $detail->assertSee('images/company-worker/3.png', false);
        $detail->assertSee('images/item/1.png', false);
    }

    public function test_company_worker_page_renders_read_only_labour_information(): void
    {
        $response = $this->get('/company/worker/' . $this->workerId);

        $response->assertOk();
        $response->assertSee($this->prefix . '_worker');
        $response->assertSee($this->prefix . '_company');
        $response->assertSee($this->prefix . '_labour');
        $response->assertSee('Zuweisung');
        $response->assertSee('Produktionsgut');
    }

    public function test_company_management_links_are_visible_only_to_owner(): void
    {
        $guest = $this->get('/company/view/' . $this->companyId);
        $guest->assertOk();
        $guest->assertDontSee('/company/fire/' . $this->workerId, false);
        $guest->assertDontSee('/company/hire/' . $this->companyId, false);

        $this->actingAs(User::findOrFail($this->otherUserId));
        $visitor = $this->get('/company/view/' . $this->companyId);
        $visitor->assertOk();
        $visitor->assertDontSee('/company/fire/' . $this->workerId, false);
        $visitor->assertDontSee('/company/hire/' . $this->companyId, false);

        $this->actingAs(User::findOrFail($this->userId));
        $owner = $this->get('/company/view/' . $this->companyId);
        $owner->assertOk();
        $owner->assertSee('/company/fire/' . $this->workerId, false);
        $owner->assertSee('/company/hire/' . $this->companyId . '/3', false);
    }

    public function test_company_worker_management_links_are_visible_only_to_owner(): void
    {
        $guest = $this->get('/company/worker/' . $this->workerId);
        $guest->assertOk();
        $guest->assertDontSee('/company/fire/' . $this->workerId, false);

        $this->actingAs(User::findOrFail($this->userId));
        $owner = $this->get('/company/worker/' . $this->workerId);
        $owner->assertOk();
        $owner->assertSee('/company/fire/' . $this->workerId, false);
    }

    public function test_company_owner_can_hire_worker_from_legacy_route(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $response = $this->get('/company/hire/' . $this->companyId . '/4');

        $response->assertRedirect('/company/view/' . $this->companyId);
        $this->assertDatabaseHas('dra_company_worker', [
            'company' => $this->companyId,
            'type' => 4,
        ]);

        $detail = $this->followingRedirects()->get('/company/view/' . $this->companyId);
        $detail->assertOk();
        $detail->assertSee('Es wurde ein neuer Arbeiter namens');
    }

    public function test_non_owner_cannot_hire_worker(): void
    {
        $this->actingAs(User::findOrFail($this->otherUserId));

        $response = $this->get('/company/hire/' . $this->companyId . '/4');

        $response->assertForbidden();
        $this->assertSame(1, DB::table('dra_company_worker')->where('company', $this->companyId)->count());
    }

    public function test_company_can_only_have_one_clerk(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        DB::table('dra_company_worker')->insert([
            'name' => $this->prefix . '_clerk',
            'type' => 5,
            'company' => $this->companyId,
            'hired' => $this->timestamp,
            'paid' => $this->timestamp,
        ]);

        $response = $this->get('/company/hire/' . $this->companyId . '/5');

        $response->assertRedirect('/company/view/' . $this->companyId);
        $this->assertSame(1, DB::table('dra_company_worker')->where('company', $this->companyId)->where('type', 5)->count());

        $detail = $this->followingRedirects()->get('/company/view/' . $this->companyId);
        $detail->assertOk();
        $detail->assertSee('Dieser Betrieb verfügt bereits über einen Schreiber.');
    }

    public function test_company_owner_can_fire_worker_and_settle_unpaid_salary(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        DB::table('dra_company_worker')
            ->where('id', $this->workerId)
            ->update(['paid' => now()->timestamp - (3 * 2592000)]);

        DB::table('dra_inventory')->insert([
            'item' => 1,
            'stack' => 50000,
            'wear' => -1,
            'table__owner' => 2,
            'owner' => $this->companyId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $response = $this->get('/company/fire/' . $this->workerId);

        $response->assertRedirect('/company/view/' . $this->companyId);
        $this->assertDatabaseMissing('dra_company_worker', ['id' => $this->workerId]);
        $this->assertDatabaseMissing('dra_labour_active', ['company_worker' => $this->workerId]);
        $this->assertDatabaseHas('dra_inventory', [
            'item' => 1,
            'stack' => 0,
            'wear' => -1,
            'table__owner' => 2,
            'owner' => $this->companyId,
        ]);

        $detail = $this->followingRedirects()->get('/company/view/' . $this->companyId);
        $detail->assertOk();
        $detail->assertSee('Arbeiter');
        $detail->assertSee('erfolgreich entlassen');
        $detail->assertSee('5,00 Tuk ausbezahlt');
        $detail->assertSee('7,00 Tuk konnten nicht mehr ausbezahlt');
    }

    public function test_non_owner_cannot_fire_worker(): void
    {
        $this->actingAs(User::findOrFail($this->otherUserId));

        $response = $this->get('/company/fire/' . $this->workerId);

        $response->assertForbidden();
        $this->assertDatabaseHas('dra_company_worker', ['id' => $this->workerId]);
        $this->assertDatabaseHas('dra_labour_active', ['company_worker' => $this->workerId]);
    }

    public function test_company_owner_can_pay_due_worker_salaries(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $period = 2592000;
        $firstPaidAt = now()->timestamp - (2 * $period);
        $secondPaidAt = now()->timestamp - (3 * $period);

        DB::table('dra_company_worker')
            ->where('id', $this->workerId)
            ->update(['paid' => $firstPaidAt]);

        $secondWorkerId = DB::table('dra_company_worker')->insertGetId([
            'name' => $this->prefix . '_second_worker',
            'type' => 1,
            'company' => $this->companyId,
            'hired' => $this->timestamp,
            'paid' => $secondPaidAt,
        ]);

        DB::table('dra_inventory')->insert([
            'item' => 1,
            'stack' => 200000,
            'wear' => -1,
            'table__owner' => 2,
            'owner' => $this->companyId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $response = $this->get('/company/pay/' . $this->companyId);

        $response->assertRedirect('/company/view/' . $this->companyId);
        $this->assertDatabaseHas('dra_inventory', [
            'item' => 1,
            'stack' => 30000,
            'wear' => -1,
            'table__owner' => 2,
            'owner' => $this->companyId,
        ]);
        $this->assertDatabaseHas('dra_company_worker', [
            'id' => $this->workerId,
            'paid' => $firstPaidAt + (2 * $period),
        ]);
        $this->assertDatabaseHas('dra_company_worker', [
            'id' => $secondWorkerId,
            'paid' => $secondPaidAt + (3 * $period),
        ]);

        $detail = $this->followingRedirects()->get('/company/view/' . $this->companyId);
        $detail->assertOk();
        $detail->assertSee('2 Arbeiter mit insgesamt 17,00 Tuk ausgezahlt');
    }

    public function test_company_pay_counts_workers_that_cannot_be_fully_paid(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $period = 2592000;
        $paidAt = now()->timestamp - (2 * $period);

        DB::table('dra_company_worker')
            ->where('id', $this->workerId)
            ->update(['paid' => $paidAt]);

        $secondWorkerId = DB::table('dra_company_worker')->insertGetId([
            'name' => $this->prefix . '_unpaid_worker',
            'type' => 1,
            'company' => $this->companyId,
            'hired' => $this->timestamp,
            'paid' => $paidAt,
        ]);

        DB::table('dra_inventory')->insert([
            'item' => 1,
            'stack' => 80000,
            'wear' => -1,
            'table__owner' => 2,
            'owner' => $this->companyId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);

        $response = $this->get('/company/pay/' . $this->companyId);

        $response->assertRedirect('/company/view/' . $this->companyId);
        $this->assertDatabaseHas('dra_inventory', [
            'item' => 1,
            'stack' => 0,
            'wear' => -1,
            'table__owner' => 2,
            'owner' => $this->companyId,
        ]);
        $this->assertDatabaseHas('dra_company_worker', [
            'id' => $this->workerId,
            'paid' => $paidAt + (2 * $period),
        ]);
        $this->assertDatabaseHas('dra_company_worker', [
            'id' => $secondWorkerId,
            'paid' => $paidAt,
        ]);

        $detail = $this->followingRedirects()->get('/company/view/' . $this->companyId);
        $detail->assertOk();
        $detail->assertSee('1 Arbeiter mit insgesamt 8,00 Tuk ausgezahlt');
        $detail->assertSee('1 Arbeiter konnten nicht ausgezahlt werden');
    }

    public function test_company_pay_requires_owner_and_money(): void
    {
        $this->actingAs(User::findOrFail($this->otherUserId));

        $forbidden = $this->get('/company/pay/' . $this->companyId);
        $forbidden->assertForbidden();

        $this->actingAs(User::findOrFail($this->userId));
        $response = $this->get('/company/pay/' . $this->companyId);

        $response->assertRedirect('/company/view/' . $this->companyId);

        $detail = $this->followingRedirects()->get('/company/view/' . $this->companyId);
        $detail->assertOk();
        $detail->assertSee('Der Betrieb verfügt über keine Mittel');
    }

    public function test_worker_page_shows_assign_labour_form_for_possible_labours(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->prepareAssignableLabour();

        $response = $this->get('/company/worker/' . $this->workerId);

        $response->assertOk();
        $response->assertSee('Arbeit zuweisen');
        $response->assertSee($this->prefix . '_labour');
        $response->assertSee($this->prefix . '_item');
        $response->assertSee($this->prefix . '_tool');
        $response->assertSee($this->prefix . '_output');
        $response->assertSee('/company/worker/' . $this->workerId, false);
    }

    public function test_owner_can_assign_labour_and_inventory_is_consumed_or_reserved(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->prepareAssignableLabour();
        DB::table('dra_labour_active')->where('company_worker', $this->workerId)->delete();

        $response = $this->post('/company/worker/' . $this->workerId, [
            'labour' => $this->labourId,
            'quantity' => 0,
            'quantity_count' => 2,
            'instances' => 99,
            'prodas' => 0,
            'prodas_value' => 25,
            'assignlabour' => 1,
        ]);

        $response->assertRedirect('/company/worker/' . $this->workerId);
        $this->assertDatabaseHas('dra_labour_active', [
            'company_worker' => $this->workerId,
            'labour' => $this->labourId,
            'prodas' => 25,
            'quantity' => 2,
            'instances' => 2,
            'nextinsta' => 0,
        ]);
        $this->assertDatabaseHas('dra_inventory', [
            'item' => $this->itemId,
            'stack' => 1,
            'wear' => -2,
            'table__owner' => 2,
            'owner' => $this->companyId,
        ]);
        $this->assertDatabaseHas('dra_inventory', [
            'item' => $this->toolItemId,
            'wear' => -3,
            'table__owner' => 2,
            'owner' => $this->companyId,
        ]);

        $detail = $this->followingRedirects()->get('/company/worker/' . $this->workerId);
        $detail->assertOk();
        $detail->assertSee('Die Tätigkeit ' . $this->prefix . '_labour wurde zugewiesen.');
    }

    public function test_non_owner_cannot_assign_labour(): void
    {
        $this->actingAs(User::findOrFail($this->otherUserId));
        $this->prepareAssignableLabour();

        $response = $this->post('/company/worker/' . $this->workerId, [
            'labour' => $this->labourId,
            'quantity' => 0,
            'quantity_count' => 1,
            'instances' => 1,
            'prodas' => -2,
            'assignlabour' => 1,
        ]);

        $response->assertForbidden();
        $this->assertSame(1, DB::table('dra_labour_active')->where('company_worker', $this->workerId)->count());
    }

    public function test_assign_labour_fails_when_required_inventory_is_missing(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->prepareAssignableLabour();
        DB::table('dra_inventory')
            ->where('item', $this->itemId)
            ->where('owner', $this->companyId)
            ->where('table__owner', 2)
            ->update(['stack' => 1]);

        $response = $this->post('/company/worker/' . $this->workerId, [
            'labour' => $this->labourId,
            'quantity' => 0,
            'quantity_count' => 1,
            'instances' => 1,
            'prodas' => -2,
            'assignlabour' => 1,
        ]);

        $response->assertRedirect('/company/worker/' . $this->workerId);
        $this->assertSame(1, DB::table('dra_labour_active')->where('company_worker', $this->workerId)->count());

        $detail = $this->followingRedirects()->get('/company/worker/' . $this->workerId);
        $detail->assertOk();
        $detail->assertSee('benötigten Rohstoffe');
    }

    public function test_labour_processor_finishes_job_creates_outputs_and_returns_tools(): void
    {
        $this->prepareAssignableLabour();
        $now = now()->timestamp;

        DB::table('dra_company_worker')
            ->where('id', $this->workerId)
            ->update(['paid' => $now]);

        DB::table('dra_labour_active')
            ->where('company_worker', $this->workerId)
            ->update([
                'until' => $now - 120,
                'prodas' => -2,
                'quantity' => 1,
                'instances' => 1,
            ]);

        DB::table('dra_inventory')
            ->where('item', $this->toolItemId)
            ->where('owner', $this->companyId)
            ->where('table__owner', 2)
            ->update(['wear' => -3]);

        $stats = app(LabourProcessor::class)->processDue($now);

        $this->assertSame(1, $stats['finished']);
        $this->assertDatabaseMissing('dra_labour_active', ['company_worker' => $this->workerId]);
        $this->assertDatabaseHas('dra_inventory', [
            'item' => $this->itemId,
            'stack' => 1,
            'wear' => -2,
            'table__owner' => 2,
            'owner' => $this->companyId,
        ]);
        $this->assertDatabaseHas('dra_inventory', [
            'item' => $this->toolItemId,
            'wear' => -2,
            'table__owner' => 2,
            'owner' => $this->companyId,
        ]);
        $this->assertDatabaseHas('dra_inventory', [
            'item' => $this->outputItemId,
            'stack' => 1,
            'wear' => -2,
            'table__owner' => 2,
            'owner' => $this->companyId,
        ]);
    }

    public function test_labour_processor_skips_workers_without_recent_pay(): void
    {
        $this->prepareAssignableLabour();
        $now = now()->timestamp;

        DB::table('dra_company_worker')
            ->where('id', $this->workerId)
            ->update(['paid' => $now - 7776001]);

        DB::table('dra_labour_active')
            ->where('company_worker', $this->workerId)
            ->update(['until' => $now - 120]);

        $stats = app(LabourProcessor::class)->processDue($now);

        $this->assertGreaterThanOrEqual(1, $stats['skipped_unpaid']);
        $this->assertDatabaseHas('dra_labour_active', ['company_worker' => $this->workerId]);
        $this->assertDatabaseMissing('dra_inventory', [
            'item' => $this->outputItemId,
            'owner' => $this->companyId,
            'table__owner' => 2,
        ]);
    }

    private function prepareAssignableLabour(): void
    {
        DB::table('dra_labour_component')->insert([
            [
                'labour' => $this->labourId,
                'item' => $this->itemId,
                'quantity' => 2,
                'type' => 0,
            ],
            [
                'labour' => $this->labourId,
                'item' => $this->toolItemId,
                'quantity' => 1,
                'type' => 1,
            ],
            [
                'labour' => $this->labourId,
                'item' => $this->outputItemId,
                'quantity' => 1,
                'type' => 2,
            ],
        ]);

        DB::table('dra_inventory')->insert([
            'item' => $this->toolItemId,
            'stack' => 0,
            'wear' => -2,
            'table__owner' => 2,
            'owner' => $this->companyId,
            'timelastvalue' => $this->timestamp,
            'data' => '',
        ]);
    }
}
