<?php

namespace Tests\Feature;

use App\Data\Economy\InventoryStateChange;
use App\Jobs\GenerateMarkdownExport;
use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\Board\ThreadScene;
use App\Models\Economy\Inventory;
use App\Models\Territory\Location;
use App\Models\Territory\Territory;
use App\Models\User;
use App\Models\User\Character;
use App\Services\InventoryService;
use App\Services\MarkdownArchiveExporter;
use App\Services\PermissionService;
use App\Support\InventoryStockState;
use App\Support\PermissionEntityType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use ZipArchive;

class ForumModelTest extends TestCase
{
    private string $prefix;

    private int $parentBoardId;

    private int $childBoardId;

    private int $otherBoardId;

    private int $userId;

    private int $characterId;

    private int $secondCharacterId;

    private int $threadId;

    private int $postId;

    private int $postTime;

    private int $locationId;

    private int $originalSetThreadSceneStandard;

    private int $originalEndThreadSceneStandard;

    private int $exportMarkdownPermitId;

    private ?int $originalExportMarkdownPermitStandard;

    private ?int $originalExportMarkdownAdminPermissionValue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prefix = 'ct_forum_'.substr(str_replace('.', '_', uniqid('', true)), 0, 12);
        $this->postTime = now()->subHour()->timestamp;
        $this->originalSetThreadSceneStandard = (int) DB::table('permits')->where('name', 'setthreadscene')->value('standard');
        $this->originalEndThreadSceneStandard = (int) DB::table('permits')->where('name', 'endthreadscene')->value('standard');
        $this->rememberAndGrantMarkdownExportPermit();

        $user = User::factory()->create([
            'name' => $this->prefix.'_user',
            'password' => 'secret',
            'email' => $this->prefix.'@example.test',
            'regemail' => $this->prefix.'@example.test',
            'regdate' => $this->postTime,
            'lastvisit' => $this->postTime,
            'lastactivity' => $this->postTime,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'wohnort' => '',
        ]);
        $this->userId = $user->id;

        $character = Character::factory()->create([
            'name' => $this->prefix.'_character',
            'regdate' => $this->postTime,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'user_id' => $this->userId,
        ]);
        $this->characterId = $character->id;

        $secondCharacter = Character::factory()->create([
            'name' => $this->prefix.'_second_character',
            'regdate' => $this->postTime + 100,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'user_id' => $this->userId,
        ]);
        $this->secondCharacterId = $secondCharacter->id;

        DB::table('group_user')->insert([
            'user_id' => $this->userId,
            'group_id' => 2,
        ]);

        $parentBoard = Board::factory()->category()->create([
            'parent_id' => 0,
            'name' => $this->prefix.'_parent',
            'password' => '',
            'description' => '',
            'sort' => 1,
            'cat' => 1,
        ]);
        $this->parentBoardId = $parentBoard->id;

        $childBoard = Board::factory()->create([
            'parent_id' => $this->parentBoardId,
            'name' => $this->prefix.'_child',
            'password' => '',
            'description' => '',
            'sort' => 1,
            'cat' => 0,
        ]);
        $this->childBoardId = $childBoard->id;

        $otherBoard = Board::factory()->create([
            'parent_id' => $this->parentBoardId,
            'name' => $this->prefix.'_other_child',
            'password' => '',
            'description' => '',
            'sort' => 2,
            'cat' => 0,
        ]);
        $this->otherBoardId = $otherBoard->id;

        $territoryId = (int) Territory::query()->whereDoesntHave('children')->value('id');
        $location = Location::factory()->create([
            'parent_type' => PermissionEntityType::TERRITORY->value,
            'parent_id' => $territoryId,
            'name' => $this->prefix.'_location',
            'description' => '',
            'priority' => 1,
        ]);
        $this->locationId = $location->id;

        $thread = ForumThread::factory()->create([
            'board_id' => $this->childBoardId,
            'name' => $this->prefix.'_thread',
            'first_post_at' => $this->postTime,
            'last_post_at' => $this->postTime,
        ]);
        $this->threadId = $thread->id;

        $post = Post::factory()->create([
            'board_id' => $this->childBoardId,
            'thread_id' => $this->threadId,
            'user_id' => $this->userId,
            'character_id' => $this->characterId,
            'time' => $this->postTime,
            'message' => $this->prefix.'_message',
            'smilies' => 1,
            'signature' => 0,
            'ip' => '127.0.0.1',
        ]);
        $this->postId = $post->id;

        DB::table('threads')
            ->where('id', $this->threadId)
            ->update([
                'post_count' => 1,
                'first_post_id' => $this->postId,
                'last_post_id' => $this->postId,
            ]);

        DB::table('boards')
            ->where('id', $this->childBoardId)
            ->update([
                'thread_count' => 1,
                'post_count' => 1,
                'last_post_id' => $this->postId,
                'last_post_at' => $this->postTime,
            ]);
    }

    protected function tearDown(): void
    {
        DB::table('thread_scenes')
            ->whereIn('thread_id', [$this->threadId])
            ->orWhere('location_id', $this->locationId)
            ->delete();

        $postIds = DB::table('posts')
            ->where('message', 'like', $this->prefix.'%')
            ->orWhere('thread_id', $this->threadId)
            ->pluck('id');
        $transferIds = DB::table('transfers')->whereIn('post_id', $postIds)->pluck('id');

        DB::table('inventory_mutations')
            ->where('source_type', 'transfer')
            ->whereIn('source_id', $transferIds)
            ->delete();
        DB::table('transfer_items')->whereIn('transfer_id', $transferIds)->delete();
        DB::table('transfers')->whereIn('id', $transferIds)->delete();
        DB::table('posts')->whereIn('id', $postIds)->delete();

        $companyIds = DB::table('companies')
            ->where('name', 'like', $this->prefix.'%')
            ->pluck('id');
        $companySiteIds = DB::table('company_sites')->whereIn('company_id', $companyIds)->pluck('id');
        DB::table('company_representatives')->whereIn('company_id', $companyIds)->delete();
        DB::table('company_role_events')->whereIn('company_id', $companyIds)->delete();
        DB::table('company_owners')->whereIn('company_id', $companyIds)->delete();
        DB::table('companies')->whereIn('id', $companyIds)->update(['headquarters_site_id' => null]);
        DB::table('company_sites')->whereIn('company_id', $companyIds)->delete();

        $itemIds = DB::table('items')->where('name', 'like', $this->prefix.'%')->pluck('id');
        DB::table('inventory_mutations')->whereIn('item_id', $itemIds)->delete();

        DB::table('inventories')
            ->where(function ($query) use ($companySiteIds) {
                $query->where(function ($query) {
                    $query->whereIn('owner_id', [$this->characterId, $this->secondCharacterId])
                        ->where('owner_type', PermissionEntityType::CHARACTER->value);
                })->orWhere(function ($query) {
                    $query->where('owner_id', $this->locationId)
                        ->where('owner_type', PermissionEntityType::LOCATION->value);
                })->orWhere(function ($query) use ($companySiteIds) {
                    $query->whereIn('owner_id', $companySiteIds)
                        ->where('owner_type', PermissionEntityType::COMPANY_SITE->value);
                });
            })
            ->delete();

        DB::table('items')
            ->where('name', 'like', $this->prefix.'%')
            ->delete();

        DB::table('companies')->whereIn('id', $companyIds)->delete();

        DB::table('threads')
            ->where('name', 'like', $this->prefix.'%')
            ->delete();

        DB::table('locations')
            ->where('name', 'like', $this->prefix.'%')
            ->delete();

        DB::table('pages')
            ->where('name', 'like', $this->prefix.'%')
            ->delete();

        DB::table('boards')
            ->where('name', 'like', $this->prefix.'%')
            ->delete();

        DB::table('configurations')
            ->where('recipient_type', 0)
            ->where('recipient_id', $this->userId)
            ->where('subject_type', 3)
            ->delete();

        DB::table('permissions')
            ->where(function ($query) {
                $query->where('recipient_id', $this->userId)
                    ->orWhereIn('subject_id', [$this->parentBoardId, $this->childBoardId, $this->otherBoardId]);
            })
            ->delete();

        DB::table('group_user')
            ->where('user_id', $this->userId)
            ->delete();

        DB::table('characters')
            ->where('name', 'like', $this->prefix.'%')
            ->delete();

        DB::table('users')
            ->where('name', 'like', $this->prefix.'%')
            ->delete();

        collect(File::glob(storage_path('app/exports/*'.$this->prefix.'*')) ?: [])
            ->each(fn (string $path) => File::delete($path));

        DB::table('permits')->where('name', 'setthreadscene')->update(['standard' => $this->originalSetThreadSceneStandard]);
        DB::table('permits')->where('name', 'endthreadscene')->update(['standard' => $this->originalEndThreadSceneStandard]);
        $this->restoreMarkdownExportPermit();
        Cache::forget('user_permits:'.$this->userId);
        Cache::forget('user_permissions:'.$this->userId);
        app()->forgetInstance(PermissionService::class);

        parent::tearDown();
    }

    public function test_company_goods_can_be_given_and_received_in_a_scene_at_its_site(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $sceneId = $this->createSceneAt($this->postTime);
        $territoryId = (int) DB::table('territories')->where('type', '5')->value('id');
        $companyId = DB::table('companies')->insertGetId([
            'name' => $this->prefix.'_scene_company',
            'type' => 3,
            'description' => '',
            'territory_id' => $territoryId,
            'thread_id' => 0,
            'volksgeld' => 0,
        ]);
        DB::table('company_owners')->insert([
            'company_id' => $companyId,
            'character_id' => $this->characterId,
            'added_by_user_id' => $this->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $companySiteId = DB::table('company_sites')->insertGetId([
            'company_id' => $companyId,
            'location_id' => $this->locationId,
            'name' => 'Hauptstandort',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('companies')->where('id', $companyId)->update(['headquarters_site_id' => $companySiteId]);
        $itemId = $this->createStackableItem($this->prefix.'_company_scene_item');
        $characterInventoryId = DB::table('inventories')->insertGetId([
            'item_id' => $itemId,
            'stack' => 3,
            'wear' => 0,
            'owner_id' => $this->characterId,
            'owner_type' => PermissionEntityType::CHARACTER->value,
            'timelastvalue' => 0,
            'data' => '',
        ]);
        $futureItemId = $this->createStackableItem($this->prefix.'_future_company_item');
        $futureInventoryId = DB::table('inventories')->insertGetId([
            'item_id' => $futureItemId,
            'stack' => 1,
            'wear' => -2,
            'owner_id' => $companySiteId,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
            'timelastvalue' => 0,
            'data' => '',
        ]);
        DB::table('inventory_mutations')->insert([
            'inventory_id' => $futureInventoryId,
            'item_id' => $futureItemId,
            'kind' => 'production',
            'clock' => 'simulation',
            'effective_at' => $this->postTime + 60,
            'source_type' => 'test',
            'source_id' => 1,
            'before_state' => null,
            'after_state' => json_encode([
                'item_id' => $futureItemId,
                'stack' => 1,
                'wear' => -2,
                'owner_type' => PermissionEntityType::COMPANY_SITE->value,
                'owner_id' => $companySiteId,
                'timelastvalue' => 0,
                'data' => '',
            ], JSON_THROW_ON_ERROR),
            'created_at' => now(),
        ]);

        $this->get('/thread/view/'.$this->threadId)
            ->assertOk()
            ->assertSee($this->prefix.'_scene_company')
            ->assertSee('company_deposit', false)
            ->assertDontSee($this->prefix.'_future_company_item');

        $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_premature_company_withdrawal',
            'transfer_action' => 'company_withdrawal',
            'company_site' => $companySiteId,
            'recipient' => $this->secondCharacterId,
            'inventory' => [$futureInventoryId => $futureInventoryId],
            'inventorystack' => [$futureInventoryId => '1'],
        ])->assertSessionHasErrors('inventory');
        $this->assertDatabaseMissing('posts', ['message' => $this->prefix.'_premature_company_withdrawal']);

        $deposit = $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_company_deposit',
            'transfer_action' => 'company_deposit',
            'company_site' => $companySiteId,
            'inventory' => [$characterInventoryId => $characterInventoryId],
            'inventorystack' => [$characterInventoryId => '2'],
        ]);
        $depositPost = Post::where('message', $this->prefix.'_company_deposit')->firstOrFail();

        $deposit->assertRedirect('/thread/view/'.$this->threadId.'/last#post'.$depositPost->id);
        $this->assertDatabaseHas('transfers', [
            'post_id' => $depositPost->id,
            'thread_scene_id' => $sceneId,
            'sender_type' => PermissionEntityType::CHARACTER->value,
            'sender_id' => $this->characterId,
            'recipient_type' => PermissionEntityType::COMPANY_SITE->value,
            'recipient_id' => $companySiteId,
            'acted_by_character_id' => $this->characterId,
        ]);
        $companyInventoryId = (int) DB::table('inventories')
            ->where('owner_type', PermissionEntityType::COMPANY_SITE->value)
            ->where('owner_id', $companySiteId)
            ->where('item_id', $itemId)
            ->where('wear', -2)
            ->value('id');
        $this->assertGreaterThan(0, $companyInventoryId);

        $this->get('/thread/view/'.$this->threadId)
            ->assertOk()
            ->assertDontSee('value="'.$companyInventoryId.'"', false);

        $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_production_company_withdrawal',
            'transfer_action' => 'company_withdrawal',
            'company_site' => $companySiteId,
            'recipient' => $this->secondCharacterId,
            'inventory' => [$companyInventoryId => $companyInventoryId],
            'inventorystack' => [$companyInventoryId => '1'],
        ])->assertSessionHasErrors('inventory');
        $this->assertDatabaseMissing('posts', ['message' => $this->prefix.'_production_company_withdrawal']);

        DB::table('inventories')->where('id', $companyInventoryId)->update([
            'wear' => InventoryStockState::RESERVED->value,
        ]);

        $this->get('/thread/view/'.$this->threadId)
            ->assertOk()
            ->assertSee('value="'.$companyInventoryId.'"', false);

        $withdrawal = $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_company_withdrawal',
            'transfer_action' => 'company_withdrawal',
            'company_site' => $companySiteId,
            'recipient' => $this->secondCharacterId,
            'inventory' => [$companyInventoryId => $companyInventoryId],
            'inventorystack' => [$companyInventoryId => '1'],
        ]);
        $withdrawalPost = Post::where('message', $this->prefix.'_company_withdrawal')->firstOrFail();

        $withdrawal->assertRedirect('/thread/view/'.$this->threadId.'/last#post'.$withdrawalPost->id);
        $this->assertDatabaseHas('transfers', [
            'post_id' => $withdrawalPost->id,
            'sender_type' => PermissionEntityType::COMPANY_SITE->value,
            'sender_id' => $companySiteId,
            'recipient_type' => PermissionEntityType::CHARACTER->value,
            'recipient_id' => $this->secondCharacterId,
            'acted_by_character_id' => $this->characterId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $itemId,
            'owner_type' => PermissionEntityType::CHARACTER->value,
            'owner_id' => $this->secondCharacterId,
            'stack' => 1,
        ]);

        $this->get('/thread/view/'.$this->threadId)
            ->assertOk()
            ->assertSee('>'.$this->prefix.'_scene_company</a>', false)
            ->assertDontSee('>Hauptstandort</a>', false);
    }

    public function test_board_thread_and_post_relationships_match_legacy_foreign_keys(): void
    {
        $parent = Board::with('children')->findOrFail($this->parentBoardId);
        $childBoard = Board::with(['parent', 'threads.lastPost', 'lastPost'])->findOrFail($this->childBoardId);
        $thread = ForumThread::with(['board', 'posts', 'firstPost', 'lastPost'])->findOrFail($this->threadId);
        $post = Post::with(['board', 'thread'])->findOrFail($this->postId);

        $this->assertTrue($parent->cat);
        $this->assertSame($this->childBoardId, $parent->children->first()->id);

        $this->assertSame($this->parentBoardId, $childBoard->parent->id);
        $this->assertSame($this->threadId, $childBoard->threads->first()->id);
        $this->assertSame($this->postId, $childBoard->lastPost->id);
        $this->assertSame($this->postTime, $childBoard->last_post_at->timestamp);

        $this->assertSame($this->childBoardId, $thread->board->id);
        $this->assertSame($this->postId, $thread->posts->first()->id);
        $this->assertSame($this->postId, $thread->firstPost->id);
        $this->assertSame($this->postId, $thread->lastPost->id);

        $this->assertSame($this->childBoardId, $post->board->id);
        $this->assertSame($this->threadId, $post->thread->id);
        $this->assertTrue($post->smilies);
        $this->assertFalse($post->signature);
        $this->assertSame($this->postTime, $post->time->timestamp);
    }

    public function test_forum_and_transfer_write_tables_use_transactional_storage(): void
    {
        $engines = DB::table('information_schema.TABLES')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->whereIn('TABLE_NAME', [
                'boards',
                'threads',
                'posts',
                'users',
                'characters',
                'inventories',
                'transfers',
                'transfer_items',
            ])
            ->pluck('ENGINE', 'TABLE_NAME');

        $this->assertCount(8, $engines);
        $this->assertSame(['InnoDB'], $engines->unique()->values()->all());
    }

    public function test_forum_counter_repair_command_restores_denormalized_counts(): void
    {
        DB::table('threads')
            ->where('id', $this->threadId)
            ->update([
                'post_count' => 99,
                'first_post_id' => 0,
                'first_post_at' => 0,
                'last_post_id' => 0,
                'last_post_at' => 0,
            ]);

        DB::table('boards')
            ->where('id', $this->childBoardId)
            ->update([
                'thread_count' => 99,
                'post_count' => 99,
                'last_post_id' => 0,
                'last_post_at' => 0,
            ]);

        DB::table('users')->where('id', $this->userId)->update(['post_count' => 99]);
        DB::table('characters')->where('id', $this->characterId)->update(['post_count' => 99]);

        $this->artisan(sprintf(
            'forum:repair-counters --threads --boards --users --characters --thread=%d --board=%d --user=%d --character=%d',
            $this->threadId,
            $this->childBoardId,
            $this->userId,
            $this->characterId,
        ))
            ->expectsOutputToContain('Rows repaired:')
            ->assertExitCode(0);

        $thread = DB::table('threads')->where('id', $this->threadId)->first();
        $board = DB::table('boards')->where('id', $this->childBoardId)->first();
        $user = DB::table('users')->where('id', $this->userId)->first();
        $character = DB::table('characters')->where('id', $this->characterId)->first();

        $this->assertSame(1, (int) $thread->post_count);
        $this->assertSame($this->postId, (int) $thread->first_post_id);
        $this->assertSame($this->postTime, (int) $thread->first_post_at);
        $this->assertSame($this->postId, (int) $thread->last_post_id);
        $this->assertSame($this->postTime, (int) $thread->last_post_at);

        $this->assertSame(1, (int) $board->thread_count);
        $this->assertSame(1, (int) $board->post_count);
        $this->assertSame($this->postId, (int) $board->last_post_id);
        $this->assertSame($this->postTime, (int) $board->last_post_at);

        $this->assertSame(1, (int) $user->post_count);
        $this->assertSame(1, (int) $character->post_count);
    }

    public function test_forum_counter_repair_command_can_dry_run_without_updating(): void
    {
        DB::table('threads')
            ->where('id', $this->threadId)
            ->update(['post_count' => 99]);

        $this->artisan('forum:repair-counters --dry-run --threads --thread='.$this->threadId)
            ->expectsOutputToContain('Mismatches found: 1')
            ->assertExitCode(0);

        $this->assertSame(99, (int) DB::table('threads')->where('id', $this->threadId)->value('post_count'));
    }

    public function test_board_export_command_outputs_markdown_archive(): void
    {
        $path = sys_get_temp_dir().'/'.$this->prefix.'_board_export.md';

        $this->artisan('forum:export-board '.$this->childBoardId.' --path='.$path)
            ->expectsOutputToContain('Export written to '.$path)
            ->assertExitCode(0);

        $markdown = File::get($path);
        File::delete($path);

        $this->assertStringContainsString('# Forum '.$this->childBoardId.': '.$this->prefix.'_child', $markdown);
        $this->assertStringContainsString('## Thema '.$this->threadId.': '.$this->prefix.'_thread', $markdown);
        $this->assertStringContainsString('### Beitrag von '.$this->prefix.'_character', $markdown);
        $this->assertStringContainsString($this->prefix.'_message', $markdown);
    }

    public function test_markdown_export_page_lists_finished_exports_and_queues_new_export(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        Queue::fake();

        $directory = storage_path('app/exports');
        File::ensureDirectoryExists($directory);
        $filename = 'markdown-export-'.$this->prefix.'.zip';
        File::put($directory.'/'.$filename, 'finished export');

        $response = $this->get('/export/markdown');

        $response->assertOk();
        $response->assertSee('Markdown-Export erstellen');
        $response->assertSee($filename);
        $response->assertSee('herunterladen');
        $response->assertSee('löschen');

        $createResponse = $this->post('/export/markdown');

        $createResponse->assertRedirect('/export/markdown');
        Queue::assertPushed(GenerateMarkdownExport::class);
    }

    public function test_markdown_export_endpoint_requires_export_permission(): void
    {
        $user = User::factory()->create([
            'name' => $this->prefix.'_no_export_user',
            'email' => $this->prefix.'_no_export@example.test',
            'regemail' => $this->prefix.'_no_export@example.test',
            'regdate' => $this->postTime,
            'lastvisit' => $this->postTime,
            'lastactivity' => $this->postTime,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'wohnort' => '',
        ]);

        $this->mock(MarkdownArchiveExporter::class, function ($mock) {
            $mock->shouldNotReceive('exportTo');
        });

        $response = $this->actingAs($user)->get('/export/markdown');

        $response->assertForbidden();
    }

    public function test_markdown_export_can_download_and_delete_finished_exports(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $directory = storage_path('app/exports');
        File::ensureDirectoryExists($directory);
        $filename = 'markdown-export-'.$this->prefix.'-download.zip';
        $path = $directory.'/'.$filename;
        File::put($path, 'finished export');

        $downloadResponse = $this->get('/export/markdown/'.$filename);

        $downloadResponse->assertOk();
        $downloadResponse->assertDownload($filename);

        $deleteResponse = $this->delete('/export/markdown/'.$filename);

        $deleteResponse->assertRedirect('/export/markdown');
        $this->assertFileDoesNotExist($path);
    }

    public function test_markdown_exporter_writes_board_and_encyclopedia_zip(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $pageId = DB::table('pages')->insertGetId([
            'sort' => 1,
            'name' => $this->prefix.'_page',
            'title' => $this->prefix.'_page_title',
            'slug' => $this->prefix.'_page',
            'page_id' => 0,
            'text' => $this->prefix.'_page_text',
            'user_id' => $this->userId,
            'created_at' => $this->postTime,
            'activated' => 1,
        ]);
        $childPageId = DB::table('pages')->insertGetId([
            'sort' => 1,
            'name' => $this->prefix.'_child_page',
            'title' => $this->prefix.'_child_page_title',
            'slug' => $this->prefix.'_child_page',
            'page_id' => $pageId,
            'text' => $this->prefix.'_child_page_text',
            'user_id' => $this->userId,
            'created_at' => $this->postTime,
            'activated' => 1,
        ]);

        $path = sys_get_temp_dir().'/'.$this->prefix.'_markdown_export.zip';
        app(MarkdownArchiveExporter::class)->exportTo($path, [$this->parentBoardId], [$pageId]);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($path));

        $threadPath = 'board/'
            .str_pad((string) $this->parentBoardId, 4, '0', STR_PAD_LEFT).' '.$this->prefix.'_parent/'
            .str_pad((string) $this->childBoardId, 4, '0', STR_PAD_LEFT).' '.$this->prefix.'_child/'
            .str_pad((string) $this->threadId, 4, '0', STR_PAD_LEFT).' '.$this->prefix.'_thread.md';
        $pagePath = 'encyclopedia/'
            .str_pad((string) $pageId, 4, '0', STR_PAD_LEFT).' '.$this->prefix.'_page.md';
        $childPagePath = 'encyclopedia/'
            .str_pad((string) $pageId, 4, '0', STR_PAD_LEFT).' '.$this->prefix.'_page/'
            .str_pad((string) $childPageId, 4, '0', STR_PAD_LEFT).' '.$this->prefix.'_child_page.md';

        $this->assertNotFalse($zip->locateName($threadPath));
        $this->assertNotFalse($zip->locateName($pagePath));
        $this->assertNotFalse($zip->locateName($childPagePath));

        $threadMarkdown = $zip->getFromName($threadPath);
        $pageMarkdown = $zip->getFromName($pagePath);
        $childPageMarkdown = $zip->getFromName($childPagePath);
        $zip->close();
        File::delete($path);

        $this->assertStringContainsString('# Thema: '.$this->prefix.'_thread (ID: '.$this->threadId.')', $threadMarkdown);
        $this->assertStringContainsString('Board: '.$this->prefix.'_child (ID: '.$this->childBoardId.')', $threadMarkdown);
        $this->assertStringContainsString('Erstellt: '.CarbonImmutable::createFromTimestamp($this->postTime, config('app.timezone'))->toIso8601String(), $threadMarkdown);
        $this->assertStringContainsString('## Beitrag 1', $threadMarkdown);
        $this->assertStringContainsString('Charakter: '.$this->prefix.'_character (ID: '.$this->characterId.')', $threadMarkdown);
        $this->assertStringContainsString($this->prefix.'_message', $threadMarkdown);
        $this->assertStringContainsString('# '.$this->prefix.'_page_title (ID: '.$pageId.')', $pageMarkdown);
        $this->assertStringContainsString($this->prefix.'_page_text', $pageMarkdown);
        $this->assertStringContainsString('# '.$this->prefix.'_child_page_title (ID: '.$childPageId.')', $childPageMarkdown);
        $this->assertStringContainsString($this->prefix.'_child_page_text', $childPageMarkdown);
    }

    public function test_forum_read_routes_render_board_filter_board_detail_and_thread(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->createExtraThreads(21);
        $this->createExtraPosts(21);

        $filterResponse = $this->get('/board/filter/date_last:1d');

        $filterResponse->assertOk();
        $filterResponse->assertSee('Forum');
        $filterResponse->assertSee($this->prefix.'_thread');
        $filterResponse->assertSee($this->prefix.'_character');
        $filterResponse->assertSee('/board/changeshow/'.$this->parentBoardId, false);
        $filterResponse->assertSee('fetch($el.href', false);
        $filterResponse->assertSee("'fa-refresh': loading", false);
        $filterResponse->assertSee("'fa-spin': loading", false);
        $filterResponse->assertSee("classList.remove('fa-toggle-down', 'fa-toggle-right')", false);
        $filterResponse->assertSee('loading = true', false);
        $filterResponse->assertSee('verkürzen');
        $filterResponse->assertSee('(Neu)');
        $filterResponse->assertSee('/board/filter/date_last:1d/2', false);
        $filterResponse->assertDontSee('?page=2', false);

        $legacyBoardResponse = $this->get('/board/view/'.$this->childBoardId);

        $legacyBoardResponse->assertRedirect('/board/filter/board:'.$this->childBoardId);

        $boardResponse = $this->get('/board/filter/board:'.$this->childBoardId);

        $boardResponse->assertOk();
        $boardResponse->assertSee($this->prefix.'_child');
        $boardResponse->assertSee($this->prefix.'_thread');
        $boardResponse->assertSee('(Neu)');
        $boardResponse->assertSee('/board/filter/board:'.$this->childBoardId.'/2', false);
        $boardResponse->assertDontSee('?page=2', false);

        $threadResponse = $this->get('/thread/view/'.$this->threadId);

        $threadResponse->assertOk();
        $threadResponse->assertSee($this->prefix.'_thread');
        $threadResponse->assertSee($this->prefix.'_character');
        $threadResponse->assertSee($this->prefix.'_message');
        $threadResponse->assertSee('/thread/view/'.$this->threadId.'/2', false);
        $threadResponse->assertSee('/thread/edit/'.$this->threadId, false);
        $threadResponse->assertSee('/thread/delete/'.$this->threadId, false);
        $threadResponse->assertSee('/post/edit/'.$this->postId, false);
        $threadResponse->assertSee('/post/delete/'.$this->postId, false);
        $threadResponse->assertSee('/thread/view/'.$this->threadId.'/last?quote='.$this->postId.'#newpost', false);
        $threadResponse->assertSee('x-data', false);
        $threadResponse->assertSee('insertThreadQuote', false);
        $threadResponse->assertDontSee('?page=2', false);
        $threadResponse->assertSee('1 Aufrufe');
        $threadResponse->assertSee('(Neu)');
        $threadResponse->assertSee('textarea-bbcode', false);
        $threadResponse->assertSee('bbcode-toolbar', false);
        $threadResponse->assertSee('window.bbcodeTextarea', false);
        $threadResponse->assertSee('[h]', false);
        $threadResponse->assertSee('Neuen Beitrag erstellen');

        $this->assertDatabaseHas('threads', [
            'id' => $this->threadId,
            'views' => 1,
        ]);

        $secondThreadResponse = $this->get('/thread/view/'.$this->threadId);

        $secondThreadResponse->assertOk();
        $secondThreadResponse->assertDontSee('(Neu)');
        $this->assertDatabaseHas('threads', [
            'id' => $this->threadId,
            'views' => 2,
        ]);

        $quotedThreadResponse = $this->get('/thread/view/'.$this->threadId.'/last?quote='.$this->postId);

        $quotedThreadResponse->assertOk();
        $quotedThreadResponse->assertSee('[q='.$this->prefix.'_character]'.$this->prefix.'_message[/q]', false);
    }

    public function test_thread_posts_render_legacy_bbcode_and_smilies_safely(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        DB::table('posts')
            ->where('id', $this->postId)
            ->update([
                'message' => '[b]'.$this->prefix.'_bold[/b]'.PHP_EOL
                    .'[h]'.$this->prefix.'_action[/h]'.PHP_EOL
                    .'[q='.$this->prefix.'_character]'.$this->prefix.'_quote[/q]'.PHP_EOL
                    .'Warten... "Hallo" - "Welt"'.PHP_EOL
                    .':) <script>alert(1)</script>',
                'smilies' => 1,
            ]);

        $response = $this->get('/thread/view/'.$this->threadId);

        $response->assertOk();
        $response->assertSee('<b>'.$this->prefix.'_bold</b>', false);
        $response->assertSee('<div class="action post">'.$this->prefix.'_action</div>', false);
        $response->assertSee('<blockquote><cite>'.$this->prefix.'_character</cite>'.$this->prefix.'_quote</blockquote>', false);
        $response->assertSee('Warten… »Hallo«', false);
        $response->assertSee('–', false);
        $response->assertSee('»Welt«', false);
        $response->assertSee('/images/emoticon/30.gif', false);
        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert(1)</script>', false);

        $this->get('/img/emoticon/30.gif')->assertRedirect('/images/emoticon/30.gif');

        DB::table('posts')
            ->where('id', $this->postId)
            ->update([
                'message' => ':)',
                'smilies' => 0,
            ]);

        $withoutSmilies = $this->get('/thread/view/'.$this->threadId);

        $withoutSmilies->assertOk();
        $withoutSmilies->assertSee(':)', false);
    }

    public function test_board_tree_toggle_persists_show_setting_and_can_be_used_without_javascript(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $response = $this->get('/board/changeshow/'.$this->parentBoardId.'/0');

        $response->assertRedirect();
        $this->assertDatabaseHas('configurations', [
            'recipient_type' => 0,
            'recipient_id' => $this->userId,
            'subject_type' => 3,
            'subject_id' => $this->parentBoardId,
            'setting' => 4,
            'value' => 0,
        ]);

        $collapsed = $this->get('/board');

        $collapsed->assertOk();
        $collapsed->assertSee('fa-toggle-right', false);
        $collapsed->assertSee('erweitern');
        $collapsed->assertSee('style="display: none;"', false);

        $ajaxResponse = $this->get('/board/changeshow/'.$this->parentBoardId.'/1/1');

        $ajaxResponse->assertOk();
        $ajaxResponse->assertSeeText('1');
        $this->assertDatabaseHas('configurations', [
            'recipient_type' => 0,
            'recipient_id' => $this->userId,
            'subject_type' => 3,
            'subject_id' => $this->parentBoardId,
            'setting' => 4,
            'value' => 1,
        ]);
    }

    public function test_online_sidebar_prunes_entries_without_existing_users(): void
    {
        DB::table('onlines')->insert([
            'time' => now()->timestamp,
            'ip' => '127.0.0.1',
            'user_id' => 999999999,
            'browser' => 'test',
            'controller' => 'Board',
            'action' => 'filter',
            'location' => null,
            'route' => 'board',
        ]);

        $this->actingAs(User::findOrFail($this->userId));

        $response = $this->get('/board');

        $response->assertOk();
        $response->assertDontSee('Unbekannter Nutzer');
        $this->assertDatabaseMissing('onlines', [
            'user_id' => 999999999,
        ]);
    }

    public function test_board_permission_readout_and_create_form_use_permission_rules(): void
    {
        $showPermitId = (int) DB::table('permits')->where('name', 'show')->value('id');
        $createPostPermitId = (int) DB::table('permits')->where('name', 'createpost')->value('id');
        $createPermissionPermitId = (int) DB::table('permits')->where('name', 'createpermission')->value('id');

        $createPostPermissionId = DB::table('permissions')->insertGetId([
            'recipient_type' => 0,
            'recipient_id' => $this->userId,
            'subject_type' => 3,
            'subject_id' => $this->childBoardId,
            'permit_id' => $createPermissionPermitId,
            'value' => 1,
        ]);

        DB::table('permissions')->insert([
            'recipient_type' => 0,
            'recipient_id' => $this->userId,
            'subject_type' => 3,
            'subject_id' => $this->childBoardId,
            'permit_id' => $createPostPermitId,
            'value' => 0,
        ]);

        $this->actingAs(User::findOrFail($this->userId));

        $readout = $this->get('/board/permissions/'.$this->childBoardId);

        $readout->assertOk();
        $readout->assertSee('Rechte des Forums');
        $readout->assertSee('Neues Recht anlegen');
        $readout->assertSee('Beiträge erstellen');
        $readout->assertSee('createpost: 0');
        $readout->assertSee($this->prefix.'_user');
        $readout->assertSee('/permission/edit/'.$createPostPermissionId, false);
        $readout->assertSee('/permission/delete/'.$createPostPermissionId, false);

        $form = $this->get('/permission/create/board/'.$this->childBoardId);

        $form->assertOk();
        $form->assertSee('Neues Recht');
        $form->assertSee('Art des Rechteempfängers');
        $form->assertSee('createpost');

        $create = $this->post('/permission/create/board/'.$this->childBoardId, [
            'recipient_type' => '0',
            'recipient_id' => (string) $this->userId,
            'permit_id' => (string) $showPermitId,
            'value' => '2',
        ]);

        $create->assertRedirect('/board/permissions/'.$this->childBoardId);
        $this->assertDatabaseHas('permissions', [
            'recipient_type' => 0,
            'recipient_id' => $this->userId,
            'subject_type' => 3,
            'subject_id' => $this->childBoardId,
            'permit_id' => $showPermitId,
            'value' => 2,
        ]);

        $editForm = $this->get('/permission/edit/'.$createPostPermissionId);

        $editForm->assertOk();
        $editForm->assertSee('Recht bearbeiten');
        $editForm->assertSee('editpermission', false);
        $editForm->assertSee('createpost');

        $edit = $this->post('/permission/edit/'.$createPostPermissionId, [
            'recipient_type' => '0',
            'recipient_id' => (string) $this->userId,
            'permit_id' => (string) $createPostPermitId,
            'value' => '1',
        ]);

        $edit->assertRedirect('/board/permissions/'.$this->childBoardId);
        $this->assertDatabaseHas('permissions', [
            'id' => $createPostPermissionId,
            'permit_id' => $createPostPermitId,
            'value' => 1,
        ]);

        $deleteForm = $this->get('/permission/delete/'.$createPostPermissionId);

        $deleteForm->assertOk();
        $deleteForm->assertSee('Recht löschen');
        $deleteForm->assertSee('deletepermission', false);

        $delete = $this->post('/permission/delete/'.$createPostPermissionId, [
            'delete' => '1',
        ]);

        $delete->assertRedirect('/board/permissions/'.$this->childBoardId);
        $this->assertDatabaseMissing('permissions', [
            'id' => $createPostPermissionId,
        ]);
    }

    public function test_post_ip_page_shows_author_ips_and_other_users_with_same_ip(): void
    {
        $viewIpPermitId = (int) DB::table('permits')->where('name', 'viewip')->value('id');
        $otherUserId = DB::table('users')->insertGetId([
            'name' => $this->prefix.'_same_ip_user',
            'password' => 'secret',
            'email' => $this->prefix.'_same_ip@example.test',
            'regemail' => $this->prefix.'_same_ip@example.test',
            'regdate' => $this->postTime,
            'lastvisit' => $this->postTime,
            'lastactivity' => $this->postTime,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'wohnort' => '',
        ]);

        DB::table('posts')->insert([
            'board_id' => $this->childBoardId,
            'thread_id' => $this->threadId,
            'user_id' => $this->userId,
            'character_id' => $this->characterId,
            'time' => $this->postTime + 1,
            'message' => $this->prefix.'_other_ip_message',
            'smilies' => 1,
            'signature' => 0,
            'ip' => '192.0.2.44',
        ]);

        DB::table('posts')->insert([
            'board_id' => $this->childBoardId,
            'thread_id' => $this->threadId,
            'user_id' => $otherUserId,
            'character_id' => $this->characterId,
            'time' => $this->postTime + 2,
            'message' => $this->prefix.'_same_ip_message',
            'smilies' => 1,
            'signature' => 0,
            'ip' => '127.0.0.1',
        ]);

        DB::table('permissions')->insert([
            'recipient_type' => 0,
            'recipient_id' => $this->userId,
            'subject_type' => 3,
            'subject_id' => $this->childBoardId,
            'permit_id' => $viewIpPermitId,
            'value' => 1,
        ]);

        $this->actingAs(User::findOrFail($this->userId));

        $threadPage = $this->get('/thread/view/'.$this->threadId);

        $threadPage->assertOk();
        $threadPage->assertSee(route('post.ip', ['post' => $this->postId]), false);

        $ipPage = $this->get('/post/ip/'.$this->postId);

        $ipPage->assertOk();
        $ipPage->assertSee('IP-Adresse dieses Beitrags');
        $ipPage->assertSee('127.0.0.1');
        $ipPage->assertSee('192.0.2.44');
        $ipPage->assertSee($this->prefix.'_same_ip_user');
        $ipPage->assertSee('Autor dieses Beitrags');
    }

    public function test_advanced_board_filters_and_autocomplete_endpoints_match_legacy_keys(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $filterPage = $this->get('/board');

        $filterPage->assertOk();
        $filterPage->assertSee('Nutzer hat Thema eröffnet');
        $filterPage->assertSee('Charakter hat im Thema geschrieben');
        $filterPage->assertSee('Erstellt am oder seit');
        $filterPage->assertSee('boardPersonFilter', false);
        $filterPage->assertSee('ajax__getusers', false);
        $filterPage->assertSee('ajax__getchars', false);

        $redirect = $this->post('/board/setfilter', [
            'user_first' => (string) $this->userId,
            'user_contains' => (string) $this->userId,
            'user_last' => (string) $this->userId,
            'char_first' => (string) $this->characterId,
            'char_contains' => (string) $this->characterId,
            'char_last' => (string) $this->characterId,
            'date_first' => '7d',
            'date_last' => '7d',
        ]);

        $redirect->assertRedirect('/board/filter/user_first:'.$this->userId
            .';user_contains:'.$this->userId
            .';user_last:'.$this->userId
            .';char_first:'.$this->characterId
            .';char_contains:'.$this->characterId
            .';char_last:'.$this->characterId
            .';date_first:7d;date_last:7d');

        $filtered = $this->get('/board/filter/user_first:'.$this->userId.';char_contains:'.$this->characterId.';date_last:7d');

        $filtered->assertOk();
        $filtered->assertSee($this->prefix.'_thread');
        $filtered->assertSee($this->prefix.'_user');
        $filtered->assertSee($this->prefix.'_character');
        $filtered->assertSee('value="'.$this->userId.'"', false);
        $filtered->assertSee('value="'.$this->characterId.'"', false);

        $userSearch = $this->getJson('/board/ajax__getusers?q='.$this->prefix);

        $userSearch->assertOk();
        $userSearch->assertJsonFragment([
            'id' => $this->userId,
            'name' => $this->prefix.'_user',
        ]);

        $characterSearch = $this->getJson('/board/ajax__getchars?q='.$this->prefix);

        $characterSearch->assertOk();
        $characterSearch->assertJsonFragment([
            'id' => $this->characterId,
            'name' => $this->prefix.'_character',
        ]);
    }

    public function test_post_can_be_created_edited_and_deleted_with_counter_updates(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $createResponse = $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_created_post',
            'smilies' => '1',
            'signature' => '1',
        ]);

        $createdPost = Post::where('message', $this->prefix.'_created_post')->firstOrFail();

        $createResponse->assertRedirect('/thread/view/'.$this->threadId.'/last#post'.$createdPost->id);
        $this->assertDatabaseHas('threads', [
            'id' => $this->threadId,
            'post_count' => 2,
            'last_post_id' => $createdPost->id,
        ]);
        $this->assertDatabaseHas('boards', [
            'id' => $this->childBoardId,
            'post_count' => 2,
            'last_post_id' => $createdPost->id,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->userId,
            'post_count' => 2,
        ]);
        $this->assertDatabaseHas('characters', [
            'id' => $this->characterId,
            'post_count' => 2,
        ]);

        $viewResponse = $this->get('/post/view/'.$createdPost->id);

        $viewResponse->assertRedirect('/thread/view/'.$this->threadId.'#post'.$createdPost->id);

        $editPage = $this->get('/post/edit/'.$createdPost->id);

        $editPage->assertOk();
        $editPage->assertSee('Beitrag bearbeiten');
        $editPage->assertSee($this->prefix.'_created_post');

        $editResponse = $this->post('/post/edit/'.$createdPost->id, [
            'character' => $this->secondCharacterId,
            'message' => $this->prefix.'_edited_post',
        ]);

        $editResponse->assertRedirect('/thread/view/'.$this->threadId.'#post'.$createdPost->id);
        $this->assertDatabaseHas('posts', [
            'id' => $createdPost->id,
            'character_id' => $this->secondCharacterId,
            'message' => $this->prefix.'_edited_post',
        ]);
        $this->assertDatabaseHas('characters', [
            'id' => $this->characterId,
            'post_count' => 1,
        ]);
        $this->assertDatabaseHas('characters', [
            'id' => $this->secondCharacterId,
            'post_count' => 1,
        ]);

        $deletePage = $this->get('/post/delete/'.$createdPost->id);

        $deletePage->assertOk();
        $deletePage->assertSee('Beitrag löschen');
        $deletePage->assertSee($this->prefix.'_edited_post');

        $deleteResponse = $this->post('/post/delete/'.$createdPost->id, [
            'delete' => '1',
        ]);

        $deleteResponse->assertRedirect('/thread/view/'.$this->threadId);
        $this->assertDatabaseMissing('posts', [
            'id' => $createdPost->id,
        ]);
        $this->assertDatabaseHas('threads', [
            'id' => $this->threadId,
            'post_count' => 1,
            'last_post_id' => $this->postId,
        ]);
        $this->assertDatabaseHas('boards', [
            'id' => $this->childBoardId,
            'post_count' => 1,
            'last_post_id' => $this->postId,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->userId,
            'post_count' => 1,
        ]);
        $this->assertDatabaseHas('characters', [
            'id' => $this->secondCharacterId,
            'post_count' => 0,
        ]);
    }

    public function test_post_create_can_create_a_new_character_inline(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $threadPage = $this->get('/thread/view/'.$this->threadId);

        $threadPage->assertOk();
        $threadPage->assertSee('id="char-new"', false);
        $threadPage->assertSee('name="newcharname"', false);

        $createResponse = $this->post('/post/create/'.$this->threadId, [
            'character' => 'new',
            'newcharname' => $this->prefix.'_inline_character',
            'message' => $this->prefix.'_inline_character_post',
            'smilies' => '1',
            'signature' => '1',
        ]);

        $newCharacterId = DB::table('characters')
            ->where('name', $this->prefix.'_inline_character')
            ->value('id');
        $createdPost = Post::where('message', $this->prefix.'_inline_character_post')->firstOrFail();

        $createResponse->assertRedirect('/thread/view/'.$this->threadId.'/last#post'.$createdPost->id);
        $this->assertDatabaseHas('characters', [
            'id' => $newCharacterId,
            'user_id' => $this->userId,
            'usertext' => '',
            'post_count' => 1,
        ]);
        $this->assertDatabaseHas('posts', [
            'id' => $createdPost->id,
            'character_id' => $newCharacterId,
            'user_id' => $this->userId,
            'message' => $this->prefix.'_inline_character_post',
        ]);
        $this->assertDatabaseHas('threads', [
            'id' => $this->threadId,
            'post_count' => 2,
            'last_post_id' => $createdPost->id,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->userId,
            'post_count' => 2,
        ]);
    }

    public function test_thread_scene_can_be_set_changed_ended_and_is_listed_on_location(): void
    {
        $this->setPermitStandard('setthreadscene', 2);
        $this->setPermitStandard('endthreadscene', 2);
        $this->actingAs(User::findOrFail($this->userId));

        $sceneForm = $this->get('/thread/scene/create/'.$this->threadId);
        $sceneForm->assertOk();
        $sceneForm->assertSee($this->prefix.'_location');
        $sceneForm->assertSee('type="datetime-local"', false);

        $startedAt = CarbonImmutable::createFromTimestamp($this->postTime + 10, config('app.timezone'))->setSecond(0);
        $setScene = $this->post('/thread/scene/create/'.$this->threadId, [
            'location_id' => $this->locationId,
            'story_started_at' => $startedAt->format('Y-m-d\TH:i'),
        ]);

        $setScene->assertRedirect('/thread/view/'.$this->threadId);
        $firstSceneId = (int) DB::table('thread_scenes')
            ->where('thread_id', $this->threadId)
            ->where('location_id', $this->locationId)
            ->value('id');

        $this->assertDatabaseHas('thread_scenes', [
            'id' => $firstSceneId,
            'thread_id' => $this->threadId,
            'location_id' => $this->locationId,
            'story_started_at' => $startedAt->timestamp,
            'story_ended_at' => null,
        ]);

        $threadPage = $this->get('/thread/view/'.$this->threadId);
        $threadPage->assertOk();
        $threadPage->assertSee($this->prefix.'_location');
        $threadPage->assertSee('Szene beenden');

        $secondLocation = Location::factory()->create([
            'parent_type' => PermissionEntityType::LOCATION->value,
            'parent_id' => $this->locationId,
            'name' => $this->prefix.'_second_location',
            'description' => '',
            'priority' => 2,
        ]);

        $changedAt = CarbonImmutable::createFromTimestamp($this->postTime + 20, config('app.timezone'))->setSecond(0);
        $changeScene = $this->post('/thread/scene/create/'.$this->threadId, [
            'location_id' => $secondLocation->id,
            'story_started_at' => $changedAt->format('Y-m-d\TH:i'),
        ]);

        $changeScene->assertRedirect('/thread/view/'.$this->threadId);
        $this->assertDatabaseHas('thread_scenes', [
            'id' => $firstSceneId,
            'ends_at_post_id' => $this->postId,
            'story_ended_at' => null,
        ]);
        $this->assertNotNull(DB::table('thread_scenes')->where('id', $firstSceneId)->value('ended_at'));

        $endedAt = CarbonImmutable::createFromTimestamp($this->postTime + 30, config('app.timezone'))->setSecond(0);
        $endScene = $this->post('/thread/scene/end/'.$this->threadId, [
            'story_ended_at' => $endedAt->format('Y-m-d\TH:i'),
        ]);

        $endScene->assertRedirect('/thread/view/'.$this->threadId);
        $this->assertDatabaseHas('thread_scenes', [
            'thread_id' => $this->threadId,
            'location_id' => $secondLocation->id,
            'story_ended_at' => $endedAt->timestamp,
        ]);

        $locationPage = $this->get('/location/view/'.$this->locationId);
        $locationPage->assertOk();
        $locationPage->assertSee($this->prefix.'_thread');
    }

    public function test_thread_can_be_created_with_an_initial_scene_transactionally(): void
    {
        $this->setPermitStandard('setthreadscene', 2);
        $this->actingAs(User::findOrFail($this->userId));
        $storyStartedAt = CarbonImmutable::createFromTimestamp($this->postTime, config('app.timezone'))->setSecond(0);

        $response = $this->post('/thread/create/'.$this->childBoardId, [
            'board' => $this->childBoardId,
            'character' => $this->characterId,
            'name' => $this->prefix.'_scene_thread',
            'message' => $this->prefix.'_scene_thread_post',
            'scene_location' => $this->locationId,
            'scene_story_started_at' => $storyStartedAt->format('Y-m-d\TH:i'),
            'smilies' => '1',
            'signature' => '1',
        ]);

        $thread = ForumThread::where('name', $this->prefix.'_scene_thread')->firstOrFail();

        $response->assertRedirect('/thread/view/'.$thread->id);
        $this->assertDatabaseHas('thread_scenes', [
            'thread_id' => $thread->id,
            'location_id' => $this->locationId,
            'story_started_at' => $storyStartedAt->timestamp,
            'created_by_user_id' => $this->userId,
            'ended_at' => null,
        ]);
        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'post_count' => 1,
        ]);
        $this->assertDatabaseHas('boards', [
            'id' => $this->childBoardId,
            'thread_count' => 2,
            'post_count' => 2,
        ]);
    }

    public function test_thread_view_rescues_legacy_inventory_transfer_form(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        DB::table('thread_scenes')->insert([
            'thread_id' => $this->threadId,
            'location_id' => $this->locationId,
            'starts_at_post_id' => null,
            'ends_at_post_id' => null,
            'story_started_at' => $this->postTime,
            'story_ended_at' => null,
            'ended_at' => null,
            'created_by_user_id' => $this->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $itemId = DB::table('items')->insertGetId([
            'item' => '',
            'name' => $this->prefix.'_apple',
            'wearable' => 0,
            'stackable' => 1,
            'description' => '',
            'valueplus' => '',
            'value' => 0,
            'scriptid' => '',
            'user__for' => '',
            'unit' => '',
            'weight' => 1,
            'img' => 7,
            'data' => '',
            'user__from' => '0',
        ]);
        $inventoryId = DB::table('inventories')->insertGetId([
            'item_id' => $itemId,
            'stack' => 3,
            'wear' => 0,
            'owner_id' => $this->characterId,
            'owner_type' => 6,
            'timelastvalue' => 0,
            'data' => '',
        ]);

        $threadPage = $this->get('/thread/view/'.$this->threadId);

        $threadPage->assertOk();
        $threadPage->assertDontSee('href="#newaction"', false);
        $threadPage->assertDontSee('name="newtransfer"', false);
        $threadPage->assertDontSee('showActionPanel(window.location.hash === \'#newaction\' ? \'newaction\' : \'newpost\');', false);
        $threadPage->assertDontSee('/transfer/transfer/'.$this->threadId, false);
        $threadPage->assertSee('name="newpost"', false);
        $threadPage->assertSee('/post/create/'.$this->threadId, false);
        $threadPage->assertSee('id="char-'.$this->characterId.'"', false);
        $threadPage->assertSee('name="transfer_action"', false);
        $threadPage->assertSee('value="give"', false);
        $threadPage->assertSee('value="drop"', false);
        $threadPage->assertSee('id="inventory-'.$inventoryId.'"', false);
        $threadPage->assertSee('name="inventorystack['.$inventoryId.']"', false);
        $threadPage->assertSee('name="recipient"', false);
        $threadPage->assertSee('class="character-selector-search" x-show="! selected"', false);
        $threadPage->assertSee('Neuen Beitrag erstellen');
    }

    public function test_inventory_transfer_backend_moves_items_and_creates_action_post(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $sceneId = DB::table('thread_scenes')->insertGetId([
            'thread_id' => $this->threadId,
            'location_id' => $this->locationId,
            'starts_at_post_id' => null,
            'ends_at_post_id' => null,
            'story_started_at' => $this->postTime,
            'story_ended_at' => null,
            'ended_at' => null,
            'created_by_user_id' => $this->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $itemId = DB::table('items')->insertGetId([
            'item' => '',
            'name' => $this->prefix.'_pear',
            'wearable' => 0,
            'stackable' => 1,
            'description' => '',
            'valueplus' => '',
            'value' => 0,
            'scriptid' => '',
            'user__for' => '',
            'unit' => '',
            'weight' => 1,
            'img' => 7,
            'data' => '',
            'user__from' => '0',
        ]);
        $inventoryId = DB::table('inventories')->insertGetId([
            'item_id' => $itemId,
            'stack' => 3,
            'wear' => 0,
            'owner_id' => $this->characterId,
            'owner_type' => 6,
            'timelastvalue' => 0,
            'data' => '',
        ]);

        $response = $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_transfer_post',
            'transfer_action' => 'give',
            'inventory' => [
                $inventoryId => $inventoryId,
            ],
            'inventorystack' => [
                $inventoryId => '2',
            ],
            'recipient' => $this->secondCharacterId,
        ]);

        $postId = (int) DB::table('posts')
            ->where('thread_id', $this->threadId)
            ->where('character_id', $this->characterId)
            ->where('message', $this->prefix.'_transfer_post')
            ->value('id');
        $transferId = (int) DB::table('transfers')
            ->where('post_id', $postId)
            ->value('id');

        $response->assertRedirect('/thread/view/'.$this->threadId.'/last#post'.$postId);
        $this->assertDatabaseHas('inventories', [
            'id' => $inventoryId,
            'owner_id' => $this->characterId,
            'owner_type' => 6,
            'stack' => 1,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $itemId,
            'owner_id' => $this->secondCharacterId,
            'owner_type' => 6,
            'stack' => 2,
        ]);
        $this->assertDatabaseHas('posts', [
            'id' => $postId,
            'thread_id' => $this->threadId,
            'board_id' => $this->childBoardId,
            'user_id' => $this->userId,
            'character_id' => $this->characterId,
            'message' => $this->prefix.'_transfer_post',
        ]);
        $this->assertDatabaseHas('transfers', [
            'id' => $transferId,
            'post_id' => $postId,
            'thread_scene_id' => $sceneId,
            'story_at' => $this->postTime,
            'created_by_user_id' => $this->userId,
            'sender_id' => $this->characterId,
            'sender_type' => 6,
            'recipient_id' => $this->secondCharacterId,
            'recipient_type' => 6,
        ]);
        $this->assertDatabaseHas('transfer_items', [
            'transfer_id' => $transferId,
            'item_id' => $itemId,
            'stack' => 2,
        ]);
        $this->assertSame(2, DB::table('inventory_mutations')
            ->where('kind', 'transfer')
            ->where('clock', 'story')
            ->where('effective_at', $this->postTime)
            ->where('source_type', 'transfer')
            ->where('source_id', $transferId)
            ->count());

        $threadPage = $this->get('/thread/view/'.$this->threadId.'/last');

        $threadPage->assertOk();
        $threadPage->assertSee($this->prefix.'_transfer_post');
        $threadPage->assertSee($this->prefix.'_pear');
        $threadPage->assertSee('(2)');
    }

    public function test_post_can_drop_items_at_and_pick_items_up_from_the_scene_location(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        DB::table('thread_scenes')->insert([
            'thread_id' => $this->threadId,
            'location_id' => $this->locationId,
            'starts_at_post_id' => null,
            'ends_at_post_id' => null,
            'story_started_at' => $this->postTime,
            'story_ended_at' => null,
            'ended_at' => null,
            'created_by_user_id' => $this->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $itemId = DB::table('items')->insertGetId([
            'item' => '',
            'name' => $this->prefix.'_scene_item',
            'wearable' => 0,
            'stackable' => 1,
            'description' => '',
            'valueplus' => '',
            'value' => 0,
            'scriptid' => '',
            'user__for' => '',
            'unit' => '',
            'weight' => 1,
            'img' => 7,
            'data' => '',
            'user__from' => '0',
        ]);
        $characterInventoryId = DB::table('inventories')->insertGetId([
            'item_id' => $itemId,
            'stack' => 3,
            'wear' => 0,
            'owner_id' => $this->characterId,
            'owner_type' => PermissionEntityType::CHARACTER->value,
            'timelastvalue' => 0,
            'data' => '',
        ]);

        $dropResponse = $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_drop_post',
            'transfer_action' => 'drop',
            'inventory' => [$characterInventoryId => $characterInventoryId],
            'inventorystack' => [$characterInventoryId => '2'],
        ]);

        $dropPost = Post::where('message', $this->prefix.'_drop_post')->firstOrFail();
        $dropTransferId = (int) DB::table('transfers')->where('post_id', $dropPost->id)->value('id');
        $locationInventory = DB::table('inventories')
            ->where('item_id', $itemId)
            ->where('owner_type', PermissionEntityType::LOCATION->value)
            ->where('owner_id', $this->locationId)
            ->first();

        $dropResponse->assertRedirect('/thread/view/'.$this->threadId.'/last#post'.$dropPost->id);
        $this->assertNotNull($locationInventory);
        $this->assertSame(2, (int) $locationInventory->stack);
        $this->assertDatabaseHas('inventories', [
            'id' => $characterInventoryId,
            'stack' => 1,
        ]);
        $this->assertDatabaseHas('transfers', [
            'id' => $dropTransferId,
            'sender_type' => PermissionEntityType::CHARACTER->value,
            'sender_id' => $this->characterId,
            'recipient_type' => PermissionEntityType::LOCATION->value,
            'recipient_id' => $this->locationId,
        ]);

        $pickupResponse = $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_pickup_post',
            'transfer_action' => 'pickup',
            'inventory' => [$locationInventory->id => $locationInventory->id],
            'inventorystack' => [$locationInventory->id => '1'],
        ]);

        $pickupPost = Post::where('message', $this->prefix.'_pickup_post')->firstOrFail();
        $pickupTransferId = (int) DB::table('transfers')->where('post_id', $pickupPost->id)->value('id');

        $pickupResponse->assertRedirect('/thread/view/'.$this->threadId.'/last#post'.$pickupPost->id);
        $this->assertDatabaseHas('inventories', [
            'id' => $characterInventoryId,
            'stack' => 2,
        ]);
        $this->assertDatabaseHas('inventories', [
            'id' => $locationInventory->id,
            'stack' => 1,
        ]);
        $this->assertDatabaseHas('transfers', [
            'id' => $pickupTransferId,
            'sender_type' => PermissionEntityType::LOCATION->value,
            'sender_id' => $this->locationId,
            'recipient_type' => PermissionEntityType::CHARACTER->value,
            'recipient_id' => $this->characterId,
        ]);

        $threadPage = $this->get('/thread/view/'.$this->threadId.'/last');
        $threadPage->assertOk();
        $threadPage->assertSee($this->prefix.'_drop_post');
        $threadPage->assertSee($this->prefix.'_pickup_post');
        $threadPage->assertSee($this->prefix.'_scene_item');
        $threadPage->assertSee($this->prefix.'_location');
        $threadPage->assertSee('value="pickup"', false);

        $locationPage = $this->get('/location/view/'.$this->locationId);
        $locationPage->assertOk();
        $locationPage->assertSee('Gegenstände');
        $locationPage->assertSee($this->prefix.'_scene_item');
        $locationPage->assertSee('Transaktionen');
        $locationPage->assertSee('/post/view/'.$dropPost->id, false);
    }

    public function test_last_transfer_post_can_be_reversed_within_five_minutes(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $sceneId = $this->createSceneAt($this->postTime);
        $itemId = $this->createStackableItem($this->prefix.'_reversible_item');
        $inventoryId = $this->createInventory($itemId, 3, PermissionEntityType::CHARACTER, $this->characterId);

        $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_reversible_post',
            'transfer_action' => 'give',
            'inventory' => [$inventoryId => $inventoryId],
            'inventorystack' => [$inventoryId => '2'],
            'recipient' => $this->secondCharacterId,
        ])->assertSessionHasNoErrors();

        $post = Post::where('message', $this->prefix.'_reversible_post')->firstOrFail();
        $transferId = (int) $post->transfers()->value('id');

        $this->get('/thread/view/'.$this->threadId.'/last')
            ->assertOk()
            ->assertSee('/transfer/'.$transferId.'/reverse', false);

        $response = $this->post('/transfer/'.$transferId.'/reverse');
        $response->assertRedirect('/post/view/'.$post->id);

        $reversalId = (int) DB::table('transfers')->where('reversal_of_transfer_id', $transferId)->value('id');
        $this->assertGreaterThan(0, $reversalId);
        $this->assertDatabaseHas('transfers', [
            'id' => $reversalId,
            'reversal_of_transfer_id' => $transferId,
            'post_id' => $post->id,
            'thread_scene_id' => $sceneId,
            'story_at' => $this->postTime,
            'sender_type' => PermissionEntityType::CHARACTER->value,
            'sender_id' => $this->secondCharacterId,
            'recipient_type' => PermissionEntityType::CHARACTER->value,
            'recipient_id' => $this->characterId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'item_id' => $itemId,
            'owner_type' => PermissionEntityType::CHARACTER->value,
            'owner_id' => $this->characterId,
            'stack' => 3,
        ]);
        $this->assertDatabaseMissing('inventories', [
            'item_id' => $itemId,
            'owner_type' => PermissionEntityType::CHARACTER->value,
            'owner_id' => $this->secondCharacterId,
        ]);

        $this->get('/thread/view/'.$this->threadId.'/last')
            ->assertOk()
            ->assertSee('Rückabwicklung')
            ->assertSee('rückgängig gemacht')
            ->assertDontSee('/transfer/'.$transferId.'/reverse', false);
        $this->get('/user/character/'.$this->characterId)
            ->assertOk()
            ->assertSee('Transaktionen')
            ->assertSee($this->prefix.'_reversible_item')
            ->assertSee('/post/view/'.$post->id, false);
        $this->post('/transfer/'.$transferId.'/reverse')->assertForbidden();
    }

    public function test_changed_item_instance_cannot_be_reversed_as_an_unchanged_transfer(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->createSceneAt($this->postTime);
        $itemId = $this->createNonStackableItem($this->prefix.'_unique_blade');
        $transferredInventoryId = $this->createInventory($itemId, 0, PermissionEntityType::CHARACTER, $this->characterId);
        $otherInventoryId = $this->createInventory($itemId, 0, PermissionEntityType::CHARACTER, $this->characterId);
        DB::table('inventories')->where('id', $transferredInventoryId)->update(['data' => 'condition:pristine']);
        DB::table('inventories')->where('id', $otherInventoryId)->update(['data' => 'condition:pristine']);

        $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_unique_transfer',
            'transfer_action' => 'give',
            'inventory' => [$transferredInventoryId => $transferredInventoryId],
            'recipient' => $this->secondCharacterId,
        ])->assertSessionHasNoErrors();

        $post = Post::where('message', $this->prefix.'_unique_transfer')->firstOrFail();
        $transferId = (int) $post->transfers()->value('id');
        $this->assertDatabaseHas('transfer_items', [
            'transfer_id' => $transferId,
            'item_id' => $itemId,
            'inventory_id' => $transferredInventoryId,
        ]);
        $this->assertDatabaseHas('inventories', [
            'id' => $transferredInventoryId,
            'owner_id' => $this->secondCharacterId,
            'data' => 'condition:pristine',
        ]);
        $this->assertDatabaseHas('inventories', [
            'id' => $otherInventoryId,
            'owner_id' => $this->characterId,
        ]);

        DB::table('inventories')->where('id', $transferredInventoryId)->update(['data' => 'condition:damaged']);

        $this->get('/thread/view/'.$this->threadId.'/last')
            ->assertOk()
            ->assertDontSee('/transfer/'.$transferId.'/reverse', false);
        $this->post('/transfer/'.$transferId.'/reverse')->assertForbidden();
        $this->assertDatabaseMissing('transfers', ['reversal_of_transfer_id' => $transferId]);
        $this->assertDatabaseHas('inventories', [
            'id' => $transferredInventoryId,
            'owner_id' => $this->secondCharacterId,
            'data' => 'condition:damaged',
        ]);
    }

    public function test_scene_identity_is_immutable_after_transfer_but_scene_can_end(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $sceneId = $this->createSceneAt($this->postTime);
        $itemId = $this->createStackableItem($this->prefix.'_scene_lock_item');
        $inventoryId = $this->createInventory($itemId, 1, PermissionEntityType::CHARACTER, $this->characterId);

        $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_scene_lock_transfer',
            'transfer_action' => 'give',
            'inventory' => [$inventoryId => $inventoryId],
            'recipient' => $this->secondCharacterId,
        ])->assertSessionHasNoErrors();

        $scene = ThreadScene::findOrFail($sceneId);

        try {
            $scene->update(['story_started_at' => $this->postTime + 60]);
            $this->fail('A transfer-bearing scene allowed its story start to change.');
        } catch (\LogicException) {
            $scene = $scene->fresh();
            $this->assertSame($this->postTime, $scene->story_started_at);
        }

        $scene->update([
            'ends_at_post_id' => ForumThread::findOrFail($this->threadId)->last_post_id,
            'story_ended_at' => $this->postTime + 300,
            'ended_at' => now(),
        ]);
        $this->assertFalse($scene->fresh()->isActive());
    }

    public function test_inventory_audit_reports_scene_story_time_drift(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->createSceneAt($this->postTime);
        $itemId = $this->createStackableItem($this->prefix.'_audit_item');
        $inventoryId = $this->createInventory($itemId, 1, PermissionEntityType::CHARACTER, $this->characterId);

        $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_audit_transfer',
            'transfer_action' => 'give',
            'inventory' => [$inventoryId => $inventoryId],
            'recipient' => $this->secondCharacterId,
        ])->assertSessionHasNoErrors();
        $post = Post::where('message', $this->prefix.'_audit_transfer')->firstOrFail();
        $transferId = (int) $post->transfers()->value('id');

        $this->artisan('economy:audit-inventory --transfer='.$transferId)
            ->expectsOutputToContain('Issues found: 0')
            ->assertExitCode(0);

        DB::table('transfers')->where('id', $transferId)->update(['story_at' => $this->postTime + 1]);

        $this->artisan('economy:audit-inventory --transfer='.$transferId)
            ->expectsOutputToContain('story time differs')
            ->expectsOutputToContain('Issues found: 1')
            ->assertExitCode(1);
    }

    public function test_inventory_audit_reports_unrecorded_state_changes(): void
    {
        $itemId = $this->createNonStackableItem($this->prefix.'_state_audit_item');
        $inventoryId = $this->createInventory($itemId, 0, PermissionEntityType::CHARACTER, $this->characterId);
        $inventory = Inventory::findOrFail($inventoryId);

        $this->artisan('economy:audit-inventory --inventory='.$inventoryId)
            ->expectsOutputToContain('Issues found: 0')
            ->assertExitCode(0);

        app(InventoryService::class)->updateState(
            $inventory,
            new InventoryStateChange(data: 'condition:pristine'),
        );

        $this->assertDatabaseHas('inventory_mutations', [
            'inventory_id' => $inventoryId,
            'kind' => 'state_change',
            'clock' => 'admin',
        ]);
        $this->artisan('economy:audit-inventory --inventory='.$inventoryId)
            ->expectsOutputToContain('Issues found: 0')
            ->assertExitCode(0);

        DB::table('inventories')->where('id', $inventoryId)->update(['data' => 'condition:damaged']);

        $this->artisan('economy:audit-inventory --inventory='.$inventoryId)
            ->expectsOutputToContain('live state differs')
            ->expectsOutputToContain('Issues found: 1')
            ->assertExitCode(1);

        app(InventoryService::class)->updateState(
            $inventory,
            new InventoryStateChange(data: 'condition:repaired'),
        );

        $this->artisan('economy:audit-inventory --inventory='.$inventoryId)
            ->expectsOutputToContain('changed without a mutation')
            ->expectsOutputToContain('Issues found: 1')
            ->assertExitCode(1);
    }

    public function test_transfer_cannot_be_reversed_after_another_post_was_created(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->createSceneAt($this->postTime);
        $itemId = $this->createStackableItem($this->prefix.'_committed_exchange_item');
        $inventoryId = $this->createInventory($itemId, 1, PermissionEntityType::CHARACTER, $this->characterId);

        $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_committed_exchange_transfer',
            'transfer_action' => 'give',
            'inventory' => [$inventoryId => $inventoryId],
            'recipient' => $this->secondCharacterId,
        ])->assertSessionHasNoErrors();
        $transferPost = Post::where('message', $this->prefix.'_committed_exchange_transfer')->firstOrFail();
        $transferId = (int) $transferPost->transfers()->value('id');

        $this->post('/post/create/'.$this->threadId, [
            'character' => $this->secondCharacterId,
            'message' => $this->prefix.'_exchange_response',
        ])->assertSessionHasNoErrors();

        $this->get('/thread/view/'.$this->threadId.'/last')
            ->assertOk()
            ->assertDontSee('/transfer/'.$transferId.'/reverse', false);
        $this->post('/transfer/'.$transferId.'/reverse')->assertForbidden();
        $this->assertDatabaseMissing('transfers', ['reversal_of_transfer_id' => $transferId]);
    }

    public function test_transfer_cannot_be_reversed_after_five_minutes(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->createSceneAt($this->postTime);
        $itemId = $this->createStackableItem($this->prefix.'_expired_reversal_item');
        $inventoryId = $this->createInventory($itemId, 1, PermissionEntityType::CHARACTER, $this->characterId);

        $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_expired_reversal_post',
            'transfer_action' => 'give',
            'inventory' => [$inventoryId => $inventoryId],
            'recipient' => $this->secondCharacterId,
        ])->assertSessionHasNoErrors();
        $post = Post::where('message', $this->prefix.'_expired_reversal_post')->firstOrFail();
        $transferId = (int) $post->transfers()->value('id');

        DB::table('transfers')->where('id', $transferId)->update([
            'created_at' => now()->subMinutes(6),
            'updated_at' => now()->subMinutes(6),
        ]);

        $this->get('/thread/view/'.$this->threadId.'/last')
            ->assertOk()
            ->assertDontSee('/transfer/'.$transferId.'/reverse', false);
        $this->post('/transfer/'.$transferId.'/reverse')->assertForbidden();
        $this->assertDatabaseMissing('transfers', ['reversal_of_transfer_id' => $transferId]);
    }

    public function test_transfer_cannot_be_reversed_after_recipient_spent_item_in_another_thread(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->createSceneAt($this->postTime);
        $itemId = $this->createStackableItem($this->prefix.'_spent_reversal_item');
        $inventoryId = $this->createInventory($itemId, 1, PermissionEntityType::CHARACTER, $this->characterId);

        $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_spent_reversal_transfer',
            'transfer_action' => 'give',
            'inventory' => [$inventoryId => $inventoryId],
            'recipient' => $this->secondCharacterId,
        ])->assertSessionHasNoErrors();
        $transferPost = Post::where('message', $this->prefix.'_spent_reversal_transfer')->firstOrFail();
        $transferId = (int) $transferPost->transfers()->value('id');

        $otherThread = ForumThread::factory()->create([
            'board_id' => $this->childBoardId,
            'name' => $this->prefix.'_spending_thread',
            'first_post_at' => $this->postTime,
            'last_post_at' => $this->postTime,
        ]);
        $otherPost = Post::factory()->create([
            'board_id' => $this->childBoardId,
            'thread_id' => $otherThread->id,
            'user_id' => $this->userId,
            'character_id' => $this->secondCharacterId,
            'time' => $this->postTime,
            'message' => $this->prefix.'_spending_thread_start',
        ]);
        $otherThread->update([
            'first_post_id' => $otherPost->id,
            'last_post_id' => $otherPost->id,
            'post_count' => 1,
        ]);
        DB::table('thread_scenes')->insert([
            'thread_id' => $otherThread->id,
            'location_id' => $this->locationId,
            'starts_at_post_id' => null,
            'ends_at_post_id' => null,
            'story_started_at' => $this->postTime + 100,
            'story_ended_at' => null,
            'ended_at' => null,
            'created_by_user_id' => $this->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post('/post/create/'.$otherThread->id, [
            'character' => $this->secondCharacterId,
            'message' => $this->prefix.'_recipient_spent_item',
            'transfer_action' => 'drop',
            'inventory' => [$inventoryId => $inventoryId],
        ])->assertSessionHasNoErrors();

        $response = $this->post('/transfer/'.$transferId.'/reverse');
        $response->assertForbidden();
        $this->assertDatabaseMissing('transfers', ['reversal_of_transfer_id' => $transferId]);
        $this->assertDatabaseHas('inventories', [
            'id' => $inventoryId,
            'owner_type' => PermissionEntityType::LOCATION->value,
            'owner_id' => $this->locationId,
        ]);
    }

    public function test_items_dropped_later_are_not_available_in_an_earlier_scene(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $laterSceneId = $this->createSceneAt($this->postTime + 200);
        $itemId = $this->createStackableItem($this->prefix.'_future_drop');
        $characterInventoryId = $this->createInventory($itemId, 3, PermissionEntityType::CHARACTER, $this->characterId);

        $dropResponse = $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_future_drop_post',
            'transfer_action' => 'drop',
            'inventory' => [$characterInventoryId => $characterInventoryId],
            'inventorystack' => [$characterInventoryId => '2'],
        ]);
        $dropResponse->assertSessionHasNoErrors();

        $locationInventoryId = (int) DB::table('inventories')
            ->where('item_id', $itemId)
            ->where('owner_type', PermissionEntityType::LOCATION->value)
            ->where('owner_id', $this->locationId)
            ->value('id');

        DB::table('thread_scenes')->where('id', $laterSceneId)->update(['ended_at' => now()]);
        $this->createSceneAt($this->postTime + 100);

        $threadPage = $this->get('/thread/view/'.$this->threadId.'/last');
        $threadPage->assertOk();
        $threadPage->assertDontSee('id="inventory-'.$locationInventoryId.'"', false);
        $threadPage->assertDontSee('value="pickup"', false);

        $pickupResponse = $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_impossible_early_pickup',
            'transfer_action' => 'pickup',
            'inventory' => [$locationInventoryId => $locationInventoryId],
            'inventorystack' => [$locationInventoryId => '1'],
        ]);

        $pickupResponse->assertSessionHasErrors('inventory');
        $this->assertDatabaseMissing('posts', ['message' => $this->prefix.'_impossible_early_pickup']);
        $this->assertDatabaseHas('inventories', ['id' => $locationInventoryId, 'stack' => 2]);
    }

    public function test_character_cannot_retroactively_spend_an_item_required_by_a_later_scene(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $laterSceneId = $this->createSceneAt($this->postTime + 200);
        $itemId = $this->createStackableItem($this->prefix.'_character_timeline_item');
        $inventoryId = $this->createInventory($itemId, 1, PermissionEntityType::CHARACTER, $this->characterId);

        $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_later_give',
            'transfer_action' => 'give',
            'inventory' => [$inventoryId => $inventoryId],
            'inventorystack' => [$inventoryId => '1'],
            'recipient' => $this->secondCharacterId,
        ])->assertSessionHasNoErrors();

        $this->post('/post/create/'.$this->threadId, [
            'character' => $this->secondCharacterId,
            'message' => $this->prefix.'_later_return',
            'transfer_action' => 'give',
            'inventory' => [$inventoryId => $inventoryId],
            'inventorystack' => [$inventoryId => '1'],
            'recipient' => $this->characterId,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('inventories', [
            'id' => $inventoryId,
            'owner_type' => PermissionEntityType::CHARACTER->value,
            'owner_id' => $this->characterId,
        ]);

        DB::table('thread_scenes')->where('id', $laterSceneId)->update(['ended_at' => now()]);
        $this->createSceneAt($this->postTime + 100);

        $this->get('/thread/view/'.$this->threadId.'/last')
            ->assertOk()
            ->assertDontSee('id="inventory-'.$inventoryId.'"', false);

        $transferCount = DB::table('transfers')->count();
        $response = $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_impossible_earlier_give',
            'transfer_action' => 'give',
            'inventory' => [$inventoryId => $inventoryId],
            'inventorystack' => [$inventoryId => '1'],
            'recipient' => $this->secondCharacterId,
        ]);

        $response->assertSessionHasErrors('inventory');
        $this->assertDatabaseMissing('posts', ['message' => $this->prefix.'_impossible_earlier_give']);
        $this->assertSame($transferCount, DB::table('transfers')->count());
        $this->assertDatabaseHas('inventories', [
            'id' => $inventoryId,
            'owner_type' => PermissionEntityType::CHARACTER->value,
            'owner_id' => $this->characterId,
        ]);
    }

    public function test_deleting_a_post_with_a_transfer_only_clears_its_message(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->createSceneAt($this->postTime);
        $itemId = $this->createStackableItem($this->prefix.'_protected_transfer');
        $inventoryId = $this->createInventory($itemId, 1, PermissionEntityType::CHARACTER, $this->characterId);

        $this->post('/post/create/'.$this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix.'_protected_transfer_post',
            'transfer_action' => 'give',
            'inventory' => [$inventoryId => $inventoryId],
            'inventorystack' => [$inventoryId => '1'],
            'recipient' => $this->secondCharacterId,
        ])->assertSessionHasNoErrors();

        $post = Post::where('message', $this->prefix.'_protected_transfer_post')->firstOrFail();
        $transferId = (int) $post->transfers()->value('id');

        $this->get('/post/delete/'.$post->id)
            ->assertOk()
            ->assertSee('bleibt deshalb erhalten')
            ->assertSee('Inhalt löschen');

        $this->post('/post/delete/'.$post->id, ['delete' => '1'])
            ->assertRedirect('/thread/view/'.$this->threadId);

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'message' => '']);
        $this->assertDatabaseHas('transfers', ['id' => $transferId, 'post_id' => $post->id]);
        $this->get('/thread/delete/'.$this->threadId)->assertForbidden();
    }

    public function test_deleting_the_last_post_warns_and_deletes_the_thread_too(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $deletePage = $this->get('/post/delete/'.$this->postId);

        $deletePage->assertOk();
        $deletePage->assertSee('letzte Beitrag');
        $deletePage->assertSee('wird auch das Thema gelöscht');

        $deleteResponse = $this->post('/post/delete/'.$this->postId, [
            'delete' => '1',
        ]);

        $deleteResponse->assertRedirect('/board');
        $this->assertDatabaseMissing('posts', [
            'id' => $this->postId,
        ]);
        $this->assertDatabaseMissing('threads', [
            'id' => $this->threadId,
        ]);
        $this->assertDatabaseHas('boards', [
            'id' => $this->childBoardId,
            'thread_count' => 0,
            'post_count' => 0,
            'last_post_id' => 0,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->userId,
            'post_count' => 0,
        ]);
        $this->assertDatabaseHas('characters', [
            'id' => $this->characterId,
            'post_count' => 0,
        ]);
    }

    public function test_thread_can_be_created_edited_moved_and_deleted_with_counter_updates(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $createPage = $this->get('/thread/create/'.$this->childBoardId);

        $createPage->assertOk();
        $createPage->assertSee('Neues Thema erstellen');
        $createPage->assertSeeInOrder([
            $this->prefix.'_parent',
            '&mdash;'.$this->prefix.'_child',
            '&mdash;'.$this->prefix.'_other_child',
        ], false);
        $createPage->assertSee('value="'.$this->parentBoardId.'"  disabled', false);
        $createPage->assertSee($this->prefix.'_child');
        $createPage->assertSee('textarea-bbcode', false);

        $createResponse = $this->post('/thread/create/'.$this->childBoardId, [
            'board' => $this->childBoardId,
            'character' => $this->characterId,
            'name' => $this->prefix.'_created_thread',
            'message' => $this->prefix.'_created_thread_message',
            'important' => '1',
            'smilies' => '1',
            'signature' => '1',
        ]);

        $createdThread = ForumThread::where('name', $this->prefix.'_created_thread')->firstOrFail();
        $createdPost = Post::where('thread_id', $createdThread->id)->firstOrFail();

        $createResponse->assertRedirect('/thread/view/'.$createdThread->id);
        $this->assertDatabaseHas('threads', [
            'id' => $createdThread->id,
            'board_id' => $this->childBoardId,
            'post_count' => 1,
            'first_post_id' => $createdPost->id,
            'last_post_id' => $createdPost->id,
            'important' => 1,
        ]);
        $this->assertDatabaseHas('posts', [
            'id' => $createdPost->id,
            'board_id' => $this->childBoardId,
            'thread_id' => $createdThread->id,
            'user_id' => $this->userId,
            'character_id' => $this->characterId,
            'message' => $this->prefix.'_created_thread_message',
        ]);
        $this->assertDatabaseHas('boards', [
            'id' => $this->childBoardId,
            'thread_count' => 2,
            'post_count' => 2,
            'last_post_id' => $createdPost->id,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->userId,
            'post_count' => 2,
        ]);
        $this->assertDatabaseHas('characters', [
            'id' => $this->characterId,
            'post_count' => 2,
        ]);

        $editPage = $this->get('/thread/edit/'.$createdThread->id);

        $editPage->assertOk();
        $editPage->assertSee('Thema bearbeiten');
        $editPage->assertSee($this->prefix.'_created_thread');
        $editPage->assertSeeInOrder([
            $this->prefix.'_parent',
            '&mdash;'.$this->prefix.'_child',
            '&mdash;'.$this->prefix.'_other_child',
        ], false);

        $editResponse = $this->post('/thread/edit/'.$createdThread->id, [
            'board' => $this->otherBoardId,
            'name' => $this->prefix.'_moved_thread',
        ]);

        $editResponse->assertRedirect('/thread/view/'.$createdThread->id);
        $this->assertDatabaseHas('threads', [
            'id' => $createdThread->id,
            'board_id' => $this->otherBoardId,
            'name' => $this->prefix.'_moved_thread',
            'important' => 0,
        ]);
        $this->assertDatabaseHas('posts', [
            'id' => $createdPost->id,
            'board_id' => $this->otherBoardId,
            'thread_id' => $createdThread->id,
        ]);
        $this->assertDatabaseHas('boards', [
            'id' => $this->childBoardId,
            'thread_count' => 1,
            'post_count' => 1,
            'last_post_id' => $this->postId,
        ]);
        $this->assertDatabaseHas('boards', [
            'id' => $this->otherBoardId,
            'thread_count' => 1,
            'post_count' => 1,
            'last_post_id' => $createdPost->id,
        ]);

        $deletePage = $this->get('/thread/delete/'.$createdThread->id);

        $deletePage->assertOk();
        $deletePage->assertSee('Thema löschen');
        $deletePage->assertSee('wird 1 Beitrag');
        $deletePage->assertSee($this->prefix.'_created_thread_message');

        $deleteResponse = $this->post('/thread/delete/'.$createdThread->id, [
            'delete' => '1',
        ]);

        $deleteResponse->assertRedirect('/board');
        $this->assertDatabaseMissing('threads', [
            'id' => $createdThread->id,
        ]);
        $this->assertDatabaseMissing('posts', [
            'id' => $createdPost->id,
        ]);
        $this->assertDatabaseHas('boards', [
            'id' => $this->otherBoardId,
            'thread_count' => 0,
            'post_count' => 0,
            'last_post_id' => 0,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->userId,
            'post_count' => 1,
        ]);
        $this->assertDatabaseHas('characters', [
            'id' => $this->characterId,
            'post_count' => 1,
        ]);
    }

    private function createExtraThreads(int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            DB::table('threads')->insert([
                'board_id' => $this->childBoardId,
                'name' => $this->prefix.'_extra_thread_'.$i,
                'first_post_at' => $this->postTime - $i,
                'last_post_at' => $this->postTime - $i,
                'post_count' => 0,
            ]);
        }

        DB::table('boards')
            ->where('id', $this->childBoardId)
            ->update([
                'thread_count' => $count + 1,
            ]);
    }

    private function createSceneAt(int $storyAt): int
    {
        return (int) DB::table('thread_scenes')->insertGetId([
            'thread_id' => $this->threadId,
            'location_id' => $this->locationId,
            'starts_at_post_id' => null,
            'ends_at_post_id' => null,
            'story_started_at' => $storyAt,
            'story_ended_at' => null,
            'ended_at' => null,
            'created_by_user_id' => $this->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createStackableItem(string $name): int
    {
        return (int) DB::table('items')->insertGetId([
            'item' => '',
            'name' => $name,
            'wearable' => 0,
            'stackable' => 1,
            'description' => '',
            'valueplus' => '',
            'value' => 0,
            'scriptid' => '',
            'user__for' => '',
            'unit' => '',
            'weight' => 1,
            'img' => 7,
            'data' => '',
            'user__from' => '0',
        ]);
    }

    private function createNonStackableItem(string $name): int
    {
        return (int) DB::table('items')->insertGetId([
            'item' => '',
            'name' => $name,
            'wearable' => 1,
            'stackable' => 0,
            'description' => '',
            'valueplus' => '',
            'value' => 0,
            'scriptid' => '',
            'user__for' => '',
            'unit' => '',
            'weight' => 1,
            'img' => 7,
            'data' => '',
            'user__from' => '0',
        ]);
    }

    private function createInventory(
        int $itemId,
        int $stack,
        PermissionEntityType $ownerType,
        int $ownerId,
    ): int {
        return (int) DB::table('inventories')->insertGetId([
            'item_id' => $itemId,
            'stack' => $stack,
            'wear' => 0,
            'owner_id' => $ownerId,
            'owner_type' => $ownerType->value,
            'timelastvalue' => 0,
            'data' => '',
        ]);
    }

    private function createExtraPosts(int $count): void
    {
        $lastPostId = $this->postId;

        for ($i = 1; $i <= $count; $i++) {
            $lastPostId = DB::table('posts')->insertGetId([
                'board_id' => $this->childBoardId,
                'thread_id' => $this->threadId,
                'user_id' => $this->userId,
                'character_id' => $this->characterId,
                'time' => $this->postTime + $i,
                'message' => $this->prefix.'_extra_message_'.$i,
                'smilies' => 1,
                'signature' => 0,
                'ip' => '127.0.0.1',
            ]);
        }

        DB::table('threads')
            ->where('id', $this->threadId)
            ->update([
                'post_count' => $count + 1,
                'last_post_id' => $lastPostId,
                'last_post_at' => $this->postTime + $count,
            ]);
    }

    private function setPermitStandard(string $name, int $standard): void
    {
        DB::table('permits')->where('name', $name)->update(['standard' => $standard]);
        Cache::forget('user_permits:'.$this->userId);
        Cache::forget('user_permissions:'.$this->userId);
        app()->forgetInstance(PermissionService::class);
    }

    private function rememberAndGrantMarkdownExportPermit(): void
    {
        $permit = DB::table('permits')->where('name', 'exportmarkdown')->first();
        $this->originalExportMarkdownPermitStandard = $permit ? (int) $permit->standard : null;

        DB::table('permits')->updateOrInsert(
            ['name' => 'exportmarkdown'],
            ['standard' => 0],
        );

        $this->exportMarkdownPermitId = (int) DB::table('permits')->where('name', 'exportmarkdown')->value('id');

        $permission = DB::table('permissions')
            ->where('recipient_type', PermissionEntityType::GROUP->value)
            ->where('recipient_id', 2)
            ->where('subject_type', 0)
            ->where('subject_id', 0)
            ->where('permit_id', $this->exportMarkdownPermitId)
            ->first();
        $this->originalExportMarkdownAdminPermissionValue = $permission ? (int) $permission->value : null;

        DB::table('permissions')->updateOrInsert(
            [
                'recipient_type' => PermissionEntityType::GROUP->value,
                'recipient_id' => 2,
                'subject_type' => 0,
                'subject_id' => 0,
                'permit_id' => $this->exportMarkdownPermitId,
            ],
            ['value' => 2],
        );

        Cache::flush();
    }

    private function restoreMarkdownExportPermit(): void
    {
        if ($this->originalExportMarkdownAdminPermissionValue === null) {
            DB::table('permissions')
                ->where('recipient_type', PermissionEntityType::GROUP->value)
                ->where('recipient_id', 2)
                ->where('subject_type', 0)
                ->where('subject_id', 0)
                ->where('permit_id', $this->exportMarkdownPermitId)
                ->delete();
        } else {
            DB::table('permissions')
                ->where('recipient_type', PermissionEntityType::GROUP->value)
                ->where('recipient_id', 2)
                ->where('subject_type', 0)
                ->where('subject_id', 0)
                ->where('permit_id', $this->exportMarkdownPermitId)
                ->update(['value' => $this->originalExportMarkdownAdminPermissionValue]);
        }

        if ($this->originalExportMarkdownPermitStandard === null) {
            DB::table('permits')->where('id', $this->exportMarkdownPermitId)->delete();
        } else {
            DB::table('permits')
                ->where('id', $this->exportMarkdownPermitId)
                ->update(['standard' => $this->originalExportMarkdownPermitStandard]);
        }

        Cache::flush();
    }
}
