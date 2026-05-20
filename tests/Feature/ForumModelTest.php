<?php

namespace Tests\Feature;

use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\User\Character;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

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

    protected function setUp(): void
    {
        parent::setUp();

        $this->prefix = 'ct_forum_' . substr(str_replace('.', '_', uniqid('', true)), 0, 12);
        $this->postTime = now()->subHour()->timestamp;

        $user = User::factory()->create([
            'name' => $this->prefix . '_user',
            'password' => 'secret',
            'email' => $this->prefix . '@example.test',
            'regemail' => $this->prefix . '@example.test',
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
            'name' => $this->prefix . '_character',
            'regdate' => $this->postTime,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'user_id' => $this->userId,
        ]);
        $this->characterId = $character->id;

        $secondCharacter = Character::factory()->create([
            'name' => $this->prefix . '_second_character',
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
            'name' => $this->prefix . '_parent',
            'password' => '',
            'description' => '',
            'sort' => 1,
            'cat' => 1,
        ]);
        $this->parentBoardId = $parentBoard->id;

        $childBoard = Board::factory()->create([
            'parent_id' => $this->parentBoardId,
            'name' => $this->prefix . '_child',
            'password' => '',
            'description' => '',
            'sort' => 1,
            'cat' => 0,
        ]);
        $this->childBoardId = $childBoard->id;

        $otherBoard = Board::factory()->create([
            'parent_id' => $this->parentBoardId,
            'name' => $this->prefix . '_other_child',
            'password' => '',
            'description' => '',
            'sort' => 2,
            'cat' => 0,
        ]);
        $this->otherBoardId = $otherBoard->id;

        $thread = ForumThread::factory()->create([
            'board_id' => $this->childBoardId,
            'name' => $this->prefix . '_thread',
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
            'message' => $this->prefix . '_message',
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
        DB::table('posts')
            ->where('message', 'like', $this->prefix . '%')
            ->delete();

        DB::table('inventories')
            ->whereIn('owner_id', [$this->characterId, $this->secondCharacterId])
            ->where('owner_type', 6)
            ->delete();

        DB::table('items')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        DB::table('threads')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        DB::table('boards')
            ->where('name', 'like', $this->prefix . '%')
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
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        DB::table('users')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        parent::tearDown();
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

        $this->artisan('forum:repair-counters --dry-run --threads --thread=' . $this->threadId)
            ->expectsOutputToContain('Mismatches found: 1')
            ->assertExitCode(0);

        $this->assertSame(99, (int) DB::table('threads')->where('id', $this->threadId)->value('post_count'));
    }

    public function test_board_export_command_outputs_markdown_archive(): void
    {
        $path = sys_get_temp_dir() . '/' . $this->prefix . '_board_export.md';

        $this->artisan('forum:export-board ' . $this->childBoardId . ' --path=' . $path)
            ->expectsOutputToContain('Export written to ' . $path)
            ->assertExitCode(0);

        $markdown = File::get($path);
        File::delete($path);

        $this->assertStringContainsString('# Forum ' . $this->childBoardId . ': ' . $this->prefix . '_child', $markdown);
        $this->assertStringContainsString('## Thema ' . $this->threadId . ': ' . $this->prefix . '_thread', $markdown);
        $this->assertStringContainsString('### Beitrag von ' . $this->prefix . '_character', $markdown);
        $this->assertStringContainsString($this->prefix . '_message', $markdown);
    }

    public function test_forum_read_routes_render_board_filter_board_detail_and_thread(): void
    {
        $this->actingAs(User::findOrFail($this->userId));
        $this->createExtraThreads(21);
        $this->createExtraPosts(21);

        $filterResponse = $this->get('/board/filter/date_last:1d');

        $filterResponse->assertOk();
        $filterResponse->assertSee('Forum');
        $filterResponse->assertSee($this->prefix . '_thread');
        $filterResponse->assertSee($this->prefix . '_character');
        $filterResponse->assertSee('/board/changeshow/' . $this->parentBoardId, false);
        $filterResponse->assertSee('fetch($el.href', false);
        $filterResponse->assertSee("'fa-refresh': loading", false);
        $filterResponse->assertSee("'fa-spin': loading", false);
        $filterResponse->assertSee("classList.remove('fa-toggle-down', 'fa-toggle-right')", false);
        $filterResponse->assertSee('loading = true', false);
        $filterResponse->assertSee('verkürzen');
        $filterResponse->assertSee('(Neu)');
        $filterResponse->assertSee('/board/filter/date_last:1d/2', false);
        $filterResponse->assertDontSee('?page=2', false);

        $legacyBoardResponse = $this->get('/board/view/' . $this->childBoardId);

        $legacyBoardResponse->assertRedirect('/board/filter/board:' . $this->childBoardId);

        $boardResponse = $this->get('/board/filter/board:' . $this->childBoardId);

        $boardResponse->assertOk();
        $boardResponse->assertSee($this->prefix . '_child');
        $boardResponse->assertSee($this->prefix . '_thread');
        $boardResponse->assertSee('(Neu)');
        $boardResponse->assertSee('/board/filter/board:' . $this->childBoardId . '/2', false);
        $boardResponse->assertDontSee('?page=2', false);

        $threadResponse = $this->get('/thread/view/' . $this->threadId);

        $threadResponse->assertOk();
        $threadResponse->assertSee($this->prefix . '_thread');
        $threadResponse->assertSee($this->prefix . '_character');
        $threadResponse->assertSee($this->prefix . '_message');
        $threadResponse->assertSee('/thread/view/' . $this->threadId . '/2', false);
        $threadResponse->assertSee('/thread/edit/' . $this->threadId, false);
        $threadResponse->assertSee('/thread/delete/' . $this->threadId, false);
        $threadResponse->assertSee('/post/edit/' . $this->postId, false);
        $threadResponse->assertSee('/post/delete/' . $this->postId, false);
        $threadResponse->assertSee('/thread/view/' . $this->threadId . '/last?quote=' . $this->postId . '#newpost', false);
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

        $secondThreadResponse = $this->get('/thread/view/' . $this->threadId);

        $secondThreadResponse->assertOk();
        $secondThreadResponse->assertDontSee('(Neu)');
        $this->assertDatabaseHas('threads', [
            'id' => $this->threadId,
            'views' => 2,
        ]);

        $quotedThreadResponse = $this->get('/thread/view/' . $this->threadId . '/last?quote=' . $this->postId);

        $quotedThreadResponse->assertOk();
        $quotedThreadResponse->assertSee('[q=' . $this->prefix . '_character]' . $this->prefix . '_message[/q]', false);
    }

    public function test_thread_posts_render_legacy_bbcode_and_smilies_safely(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        DB::table('posts')
            ->where('id', $this->postId)
            ->update([
                'message' => '[b]' . $this->prefix . '_bold[/b]' . PHP_EOL
                    . '[h]' . $this->prefix . '_action[/h]' . PHP_EOL
                    . '[q=' . $this->prefix . '_character]' . $this->prefix . '_quote[/q]' . PHP_EOL
                    . 'Warten... "Hallo" - "Welt"' . PHP_EOL
                    . ':) <script>alert(1)</script>',
                'smilies' => 1,
            ]);

        $response = $this->get('/thread/view/' . $this->threadId);

        $response->assertOk();
        $response->assertSee('<b>' . $this->prefix . '_bold</b>', false);
        $response->assertSee('<div class="action post">' . $this->prefix . '_action</div>', false);
        $response->assertSee('<blockquote><cite>' . $this->prefix . '_character</cite>' . $this->prefix . '_quote</blockquote>', false);
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

        $withoutSmilies = $this->get('/thread/view/' . $this->threadId);

        $withoutSmilies->assertOk();
        $withoutSmilies->assertSee(':)', false);
    }

    public function test_board_tree_toggle_persists_show_setting_and_can_be_used_without_javascript(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $response = $this->get('/board/changeshow/' . $this->parentBoardId . '/0');

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

        $ajaxResponse = $this->get('/board/changeshow/' . $this->parentBoardId . '/1/1');

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

        $readout = $this->get('/board/permissions/' . $this->childBoardId);

        $readout->assertOk();
        $readout->assertSee('Rechte des Forums');
        $readout->assertSee('Neues Recht anlegen');
        $readout->assertSee('Beiträge erstellen');
        $readout->assertSee('createpost: 0');
        $readout->assertSee($this->prefix . '_user');
        $readout->assertSee('/permission/edit/' . $createPostPermissionId, false);
        $readout->assertSee('/permission/delete/' . $createPostPermissionId, false);

        $form = $this->get('/permission/create/board/' . $this->childBoardId);

        $form->assertOk();
        $form->assertSee('Neues Recht');
        $form->assertSee('Art des Rechteempfängers');
        $form->assertSee('createpost');

        $create = $this->post('/permission/create/board/' . $this->childBoardId, [
            'recipient_type' => '0',
            'recipient_id' => (string) $this->userId,
            'permit_id' => (string) $showPermitId,
            'value' => '2',
        ]);

        $create->assertRedirect('/board/permissions/' . $this->childBoardId);
        $this->assertDatabaseHas('permissions', [
            'recipient_type' => 0,
            'recipient_id' => $this->userId,
            'subject_type' => 3,
            'subject_id' => $this->childBoardId,
            'permit_id' => $showPermitId,
            'value' => 2,
        ]);

        $editForm = $this->get('/permission/edit/' . $createPostPermissionId);

        $editForm->assertOk();
        $editForm->assertSee('Recht bearbeiten');
        $editForm->assertSee('editpermission', false);
        $editForm->assertSee('createpost');

        $edit = $this->post('/permission/edit/' . $createPostPermissionId, [
            'recipient_type' => '0',
            'recipient_id' => (string) $this->userId,
            'permit_id' => (string) $createPostPermitId,
            'value' => '1',
        ]);

        $edit->assertRedirect('/board/permissions/' . $this->childBoardId);
        $this->assertDatabaseHas('permissions', [
            'id' => $createPostPermissionId,
            'permit_id' => $createPostPermitId,
            'value' => 1,
        ]);

        $deleteForm = $this->get('/permission/delete/' . $createPostPermissionId);

        $deleteForm->assertOk();
        $deleteForm->assertSee('Recht löschen');
        $deleteForm->assertSee('deletepermission', false);

        $delete = $this->post('/permission/delete/' . $createPostPermissionId, [
            'delete' => '1',
        ]);

        $delete->assertRedirect('/board/permissions/' . $this->childBoardId);
        $this->assertDatabaseMissing('permissions', [
            'id' => $createPostPermissionId,
        ]);
    }

    public function test_post_ip_page_shows_author_ips_and_other_users_with_same_ip(): void
    {
        $viewIpPermitId = (int) DB::table('permits')->where('name', 'viewip')->value('id');
        $otherUserId = DB::table('users')->insertGetId([
            'name' => $this->prefix . '_same_ip_user',
            'password' => 'secret',
            'email' => $this->prefix . '_same_ip@example.test',
            'regemail' => $this->prefix . '_same_ip@example.test',
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
            'message' => $this->prefix . '_other_ip_message',
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
            'message' => $this->prefix . '_same_ip_message',
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

        $threadPage = $this->get('/thread/view/' . $this->threadId);

        $threadPage->assertOk();
        $threadPage->assertSee(route('post.ip', ['post' => $this->postId]), false);

        $ipPage = $this->get('/post/ip/' . $this->postId);

        $ipPage->assertOk();
        $ipPage->assertSee('IP-Adresse dieses Beitrags');
        $ipPage->assertSee('127.0.0.1');
        $ipPage->assertSee('192.0.2.44');
        $ipPage->assertSee($this->prefix . '_same_ip_user');
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

        $redirect->assertRedirect('/board/filter/user_first:' . $this->userId
            . ';user_contains:' . $this->userId
            . ';user_last:' . $this->userId
            . ';char_first:' . $this->characterId
            . ';char_contains:' . $this->characterId
            . ';char_last:' . $this->characterId
            . ';date_first:7d;date_last:7d');

        $filtered = $this->get('/board/filter/user_first:' . $this->userId . ';char_contains:' . $this->characterId . ';date_last:7d');

        $filtered->assertOk();
        $filtered->assertSee($this->prefix . '_thread');
        $filtered->assertSee($this->prefix . '_user');
        $filtered->assertSee($this->prefix . '_character');
        $filtered->assertSee('value="' . $this->userId . '"', false);
        $filtered->assertSee('value="' . $this->characterId . '"', false);

        $userSearch = $this->getJson('/board/ajax__getusers?q=' . $this->prefix);

        $userSearch->assertOk();
        $userSearch->assertJsonFragment([
            'id' => $this->userId,
            'name' => $this->prefix . '_user',
        ]);

        $characterSearch = $this->getJson('/board/ajax__getchars?q=' . $this->prefix);

        $characterSearch->assertOk();
        $characterSearch->assertJsonFragment([
            'id' => $this->characterId,
            'name' => $this->prefix . '_character',
        ]);
    }

    public function test_post_can_be_created_edited_and_deleted_with_counter_updates(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $createResponse = $this->post('/post/create/' . $this->threadId, [
            'character' => $this->characterId,
            'message' => $this->prefix . '_created_post',
            'smilies' => '1',
            'signature' => '1',
        ]);

        $createdPost = Post::where('message', $this->prefix . '_created_post')->firstOrFail();

        $createResponse->assertRedirect('/thread/view/' . $this->threadId . '/last#post' . $createdPost->id);
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

        $viewResponse = $this->get('/post/view/' . $createdPost->id);

        $viewResponse->assertRedirect('/thread/view/' . $this->threadId . '#post' . $createdPost->id);

        $editPage = $this->get('/post/edit/' . $createdPost->id);

        $editPage->assertOk();
        $editPage->assertSee('Beitrag bearbeiten');
        $editPage->assertSee($this->prefix . '_created_post');

        $editResponse = $this->post('/post/edit/' . $createdPost->id, [
            'character' => $this->secondCharacterId,
            'message' => $this->prefix . '_edited_post',
        ]);

        $editResponse->assertRedirect('/thread/view/' . $this->threadId . '#post' . $createdPost->id);
        $this->assertDatabaseHas('posts', [
            'id' => $createdPost->id,
            'character_id' => $this->secondCharacterId,
            'message' => $this->prefix . '_edited_post',
        ]);
        $this->assertDatabaseHas('characters', [
            'id' => $this->characterId,
            'post_count' => 1,
        ]);
        $this->assertDatabaseHas('characters', [
            'id' => $this->secondCharacterId,
            'post_count' => 1,
        ]);

        $deletePage = $this->get('/post/delete/' . $createdPost->id);

        $deletePage->assertOk();
        $deletePage->assertSee('Beitrag löschen');
        $deletePage->assertSee($this->prefix . '_edited_post');

        $deleteResponse = $this->post('/post/delete/' . $createdPost->id, [
            'delete' => '1',
        ]);

        $deleteResponse->assertRedirect('/thread/view/' . $this->threadId);
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

        $threadPage = $this->get('/thread/view/' . $this->threadId);

        $threadPage->assertOk();
        $threadPage->assertSee('id="char-new"', false);
        $threadPage->assertSee('name="newcharname"', false);

        $createResponse = $this->post('/post/create/' . $this->threadId, [
            'character' => 'new',
            'newcharname' => $this->prefix . '_inline_character',
            'message' => $this->prefix . '_inline_character_post',
            'smilies' => '1',
            'signature' => '1',
        ]);

        $newCharacterId = DB::table('characters')
            ->where('name', $this->prefix . '_inline_character')
            ->value('id');
        $createdPost = Post::where('message', $this->prefix . '_inline_character_post')->firstOrFail();

        $createResponse->assertRedirect('/thread/view/' . $this->threadId . '/last#post' . $createdPost->id);
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
            'message' => $this->prefix . '_inline_character_post',
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

    public function test_thread_view_rescues_legacy_inventory_transfer_form(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $itemId = DB::table('items')->insertGetId([
            'item' => '',
            'name' => $this->prefix . '_apple',
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

        $threadPage = $this->get('/thread/view/' . $this->threadId);

        $threadPage->assertOk();
        $threadPage->assertSee('class="activeaction" href="#newpost"', false);
        $threadPage->assertSee('href="#newaction"', false);
        $threadPage->assertSee('name="newtransfer"', false);
        $threadPage->assertSee('showActionPanel(window.location.hash === \'#newaction\' ? \'newaction\' : \'newpost\');', false);
        $threadPage->assertSee('/transfer/transfer/' . $this->threadId, false);
        $threadPage->assertSee('id="action-char-' . $this->characterId . '"', false);
        $threadPage->assertSee('id="inventory-' . $inventoryId . '"', false);
        $threadPage->assertSee('name="inventorystack[' . $inventoryId . ']"', false);
        $threadPage->assertSee('name="recipient"', false);
        $threadPage->assertSee('class="character-selector-search" x-show="! selected"', false);
        $threadPage->assertSee('Handlung ausführen');
    }

    public function test_inventory_transfer_backend_moves_items_and_creates_action_post(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $itemId = DB::table('items')->insertGetId([
            'item' => '',
            'name' => $this->prefix . '_pear',
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

        $response = $this->post('/transfer/transfer/' . $this->threadId, [
            'character' => $this->characterId,
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
            ->where('character_id', 3)
            ->value('id');
        $transferId = (int) DB::table('transfers')
            ->where('post_id', $postId)
            ->value('id');

        $response->assertRedirect('/thread/view/' . $this->threadId . '/last#post' . $postId);
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
            'user_id' => 2,
            'character_id' => 3,
            'message' => '',
        ]);
        $this->assertDatabaseHas('transfers', [
            'id' => $transferId,
            'post_id' => $postId,
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

        $threadPage = $this->get('/thread/view/' . $this->threadId . '/last');

        $threadPage->assertOk();
        $threadPage->assertSee($this->prefix . '_pear');
        $threadPage->assertSee('(2)');
    }

    public function test_deleting_the_last_post_warns_and_deletes_the_thread_too(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $deletePage = $this->get('/post/delete/' . $this->postId);

        $deletePage->assertOk();
        $deletePage->assertSee('letzte Beitrag');
        $deletePage->assertSee('wird auch das Thema gelöscht');

        $deleteResponse = $this->post('/post/delete/' . $this->postId, [
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

        $createPage = $this->get('/thread/create/' . $this->childBoardId);

        $createPage->assertOk();
        $createPage->assertSee('Neues Thema erstellen');
        $createPage->assertSeeInOrder([
            $this->prefix . '_parent',
            '&mdash;' . $this->prefix . '_child',
            '&mdash;' . $this->prefix . '_other_child',
        ], false);
        $createPage->assertSee('value="' . $this->parentBoardId . '"  disabled', false);
        $createPage->assertSee($this->prefix . '_child');
        $createPage->assertSee('textarea-bbcode', false);

        $createResponse = $this->post('/thread/create/' . $this->childBoardId, [
            'board' => $this->childBoardId,
            'character' => $this->characterId,
            'name' => $this->prefix . '_created_thread',
            'message' => $this->prefix . '_created_thread_message',
            'important' => '1',
            'smilies' => '1',
            'signature' => '1',
        ]);

        $createdThread = ForumThread::where('name', $this->prefix . '_created_thread')->firstOrFail();
        $createdPost = Post::where('thread_id', $createdThread->id)->firstOrFail();

        $createResponse->assertRedirect('/thread/view/' . $createdThread->id);
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
            'message' => $this->prefix . '_created_thread_message',
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

        $editPage = $this->get('/thread/edit/' . $createdThread->id);

        $editPage->assertOk();
        $editPage->assertSee('Thema bearbeiten');
        $editPage->assertSee($this->prefix . '_created_thread');
        $editPage->assertSeeInOrder([
            $this->prefix . '_parent',
            '&mdash;' . $this->prefix . '_child',
            '&mdash;' . $this->prefix . '_other_child',
        ], false);

        $editResponse = $this->post('/thread/edit/' . $createdThread->id, [
            'board' => $this->otherBoardId,
            'name' => $this->prefix . '_moved_thread',
        ]);

        $editResponse->assertRedirect('/thread/view/' . $createdThread->id);
        $this->assertDatabaseHas('threads', [
            'id' => $createdThread->id,
            'board_id' => $this->otherBoardId,
            'name' => $this->prefix . '_moved_thread',
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

        $deletePage = $this->get('/thread/delete/' . $createdThread->id);

        $deletePage->assertOk();
        $deletePage->assertSee('Thema löschen');
        $deletePage->assertSee('wird 1 Beitrag');
        $deletePage->assertSee($this->prefix . '_created_thread_message');

        $deleteResponse = $this->post('/thread/delete/' . $createdThread->id, [
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
                'name' => $this->prefix . '_extra_thread_' . $i,
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
                'message' => $this->prefix . '_extra_message_' . $i,
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
}
