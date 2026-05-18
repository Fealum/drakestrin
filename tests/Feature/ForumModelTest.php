<?php

namespace Tests\Feature;

use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
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

        $this->userId = DB::table('dra_user')->insertGetId([
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

        $this->characterId = DB::table('dra_character')->insertGetId([
            'name' => $this->prefix . '_character',
            'regdate' => $this->postTime,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'user' => $this->userId,
        ]);

        $this->secondCharacterId = DB::table('dra_character')->insertGetId([
            'name' => $this->prefix . '_second_character',
            'regdate' => $this->postTime + 100,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'user' => $this->userId,
        ]);

        DB::table('dra_group2user')->insert([
            'user' => $this->userId,
            'group' => 2,
        ]);

        $this->parentBoardId = DB::table('dra_board')->insertGetId([
            'board' => 0,
            'name' => $this->prefix . '_parent',
            'password' => '',
            'description' => '',
            'sort' => 1,
            'cat' => 1,
        ]);

        $this->childBoardId = DB::table('dra_board')->insertGetId([
            'board' => $this->parentBoardId,
            'name' => $this->prefix . '_child',
            'password' => '',
            'description' => '',
            'sort' => 1,
            'cat' => 0,
        ]);

        $this->otherBoardId = DB::table('dra_board')->insertGetId([
            'board' => $this->parentBoardId,
            'name' => $this->prefix . '_other_child',
            'password' => '',
            'description' => '',
            'sort' => 2,
            'cat' => 0,
        ]);

        $this->threadId = DB::table('dra_thread')->insertGetId([
            'board' => $this->childBoardId,
            'name' => $this->prefix . '_thread',
            'post__first_time' => $this->postTime,
            'post__last_time' => $this->postTime,
        ]);

        $this->postId = DB::table('dra_post')->insertGetId([
            'board' => $this->childBoardId,
            'thread' => $this->threadId,
            'user' => $this->userId,
            'character' => $this->characterId,
            'time' => $this->postTime,
            'message' => $this->prefix . '_message',
            'smilies' => 1,
            'signature' => 0,
            'ip' => '127.0.0.1',
        ]);

        DB::table('dra_thread')
            ->where('id', $this->threadId)
            ->update([
                'post__total' => 1,
                'post__first' => $this->postId,
                'post__last' => $this->postId,
            ]);

        DB::table('dra_board')
            ->where('id', $this->childBoardId)
            ->update([
                'thread__total' => 1,
                'post__total' => 1,
                'post__last' => $this->postId,
                'post__last_time' => $this->postTime,
            ]);
    }

    protected function tearDown(): void
    {
        DB::table('dra_post')
            ->where('message', 'like', $this->prefix . '%')
            ->delete();

        DB::table('dra_inventory')
            ->whereIn('owner', [$this->characterId, $this->secondCharacterId])
            ->where('table__owner', 6)
            ->delete();

        DB::table('dra_item')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        DB::table('dra_thread')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        DB::table('dra_board')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        DB::table('dra_configuration')
            ->where('table__recipient', 0)
            ->where('recipient', $this->userId)
            ->where('table__subject', 3)
            ->delete();

        DB::table('dra_permission')
            ->where(function ($query) {
                $query->where('recipient', $this->userId)
                    ->orWhereIn('subject', [$this->parentBoardId, $this->childBoardId, $this->otherBoardId]);
            })
            ->delete();

        DB::table('dra_group2user')
            ->where('user', $this->userId)
            ->delete();

        DB::table('dra_character')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        DB::table('dra_user')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        parent::tearDown();
    }

    public function test_board_thread_and_post_relationships_match_legacy_foreign_keys(): void
    {
        $parentBoard = Board::with('childBoards')->findOrFail($this->parentBoardId);
        $childBoard = Board::with(['parentBoard', 'threads.lastPost', 'lastPost'])->findOrFail($this->childBoardId);
        $thread = ForumThread::with(['boardModel', 'posts', 'firstPost', 'lastPost'])->findOrFail($this->threadId);
        $post = Post::with(['boardModel', 'threadModel'])->findOrFail($this->postId);

        $this->assertTrue($parentBoard->cat);
        $this->assertSame($this->childBoardId, $parentBoard->childBoards->first()->id);

        $this->assertSame($this->parentBoardId, $childBoard->parentBoard->id);
        $this->assertSame($this->threadId, $childBoard->threads->first()->id);
        $this->assertSame($this->postId, $childBoard->lastPost->id);
        $this->assertSame($this->postTime, $childBoard->post__last_time->timestamp);

        $this->assertSame($this->childBoardId, $thread->boardModel->id);
        $this->assertSame($this->postId, $thread->posts->first()->id);
        $this->assertSame($this->postId, $thread->firstPost->id);
        $this->assertSame($this->postId, $thread->lastPost->id);

        $this->assertSame($this->childBoardId, $post->boardModel->id);
        $this->assertSame($this->threadId, $post->threadModel->id);
        $this->assertTrue($post->smilies);
        $this->assertFalse($post->signature);
        $this->assertSame($this->postTime, $post->time->timestamp);
    }

    public function test_forum_counter_repair_command_restores_denormalized_counts(): void
    {
        DB::table('dra_thread')
            ->where('id', $this->threadId)
            ->update([
                'post__total' => 99,
                'post__first' => 0,
                'post__first_time' => 0,
                'post__last' => 0,
                'post__last_time' => 0,
            ]);

        DB::table('dra_board')
            ->where('id', $this->childBoardId)
            ->update([
                'thread__total' => 99,
                'post__total' => 99,
                'post__last' => 0,
                'post__last_time' => 0,
            ]);

        DB::table('dra_user')->where('id', $this->userId)->update(['post__total' => 99]);
        DB::table('dra_character')->where('id', $this->characterId)->update(['post__total' => 99]);

        $this->artisan(sprintf(
            'forum:repair-counters --threads --boards --users --characters --thread=%d --board=%d --user=%d --character=%d',
            $this->threadId,
            $this->childBoardId,
            $this->userId,
            $this->characterId,
        ))
            ->expectsOutputToContain('Rows repaired:')
            ->assertExitCode(0);

        $thread = DB::table('dra_thread')->where('id', $this->threadId)->first();
        $board = DB::table('dra_board')->where('id', $this->childBoardId)->first();
        $user = DB::table('dra_user')->where('id', $this->userId)->first();
        $character = DB::table('dra_character')->where('id', $this->characterId)->first();

        $this->assertSame(1, (int) $thread->post__total);
        $this->assertSame($this->postId, (int) $thread->post__first);
        $this->assertSame($this->postTime, (int) $thread->post__first_time);
        $this->assertSame($this->postId, (int) $thread->post__last);
        $this->assertSame($this->postTime, (int) $thread->post__last_time);

        $this->assertSame(1, (int) $board->thread__total);
        $this->assertSame(1, (int) $board->post__total);
        $this->assertSame($this->postId, (int) $board->post__last);
        $this->assertSame($this->postTime, (int) $board->post__last_time);

        $this->assertSame(1, (int) $user->post__total);
        $this->assertSame(1, (int) $character->post__total);
    }

    public function test_forum_counter_repair_command_can_dry_run_without_updating(): void
    {
        DB::table('dra_thread')
            ->where('id', $this->threadId)
            ->update(['post__total' => 99]);

        $this->artisan('forum:repair-counters --dry-run --threads --thread=' . $this->threadId)
            ->expectsOutputToContain('Mismatches found: 1')
            ->assertExitCode(0);

        $this->assertSame(99, (int) DB::table('dra_thread')->where('id', $this->threadId)->value('post__total'));
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

        $this->assertDatabaseHas('dra_thread', [
            'id' => $this->threadId,
            'views' => 1,
        ]);

        $secondThreadResponse = $this->get('/thread/view/' . $this->threadId);

        $secondThreadResponse->assertOk();
        $secondThreadResponse->assertDontSee('(Neu)');
        $this->assertDatabaseHas('dra_thread', [
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

        DB::table('dra_post')
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

        DB::table('dra_post')
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
        $this->assertDatabaseHas('dra_configuration', [
            'table__recipient' => 0,
            'recipient' => $this->userId,
            'table__subject' => 3,
            'subject' => $this->parentBoardId,
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
        $this->assertDatabaseHas('dra_configuration', [
            'table__recipient' => 0,
            'recipient' => $this->userId,
            'table__subject' => 3,
            'subject' => $this->parentBoardId,
            'setting' => 4,
            'value' => 1,
        ]);
    }

    public function test_online_sidebar_prunes_entries_without_existing_users(): void
    {
        DB::table('dra_online')->insert([
            'time' => now()->timestamp,
            'ip' => '127.0.0.1',
            'user' => 999999999,
            'browser' => 'test',
            'controller' => 'Board',
            'action' => 'filter',
            'table__location' => null,
            'location' => null,
            'route' => 'board',
        ]);

        $this->actingAs(User::findOrFail($this->userId));

        $response = $this->get('/board');

        $response->assertOk();
        $response->assertDontSee('Unbekannter Nutzer');
        $this->assertDatabaseMissing('dra_online', [
            'user' => 999999999,
        ]);
    }

    public function test_board_permission_readout_and_create_form_use_legacy_permission_rows(): void
    {
        $showPermitId = (int) DB::table('dra_permit')->where('name', 'show')->value('id');
        $createPostPermitId = (int) DB::table('dra_permit')->where('name', 'createpost')->value('id');
        $createPermissionPermitId = (int) DB::table('dra_permit')->where('name', 'createpermission')->value('id');

        $createPostPermissionId = DB::table('dra_permission')->insertGetId([
            'table__recipient' => 0,
            'recipient' => $this->userId,
            'table__subject' => 3,
            'subject' => $this->childBoardId,
            'permit' => $createPermissionPermitId,
            'value' => 1,
        ]);

        DB::table('dra_permission')->insert([
            'table__recipient' => 0,
            'recipient' => $this->userId,
            'table__subject' => 3,
            'subject' => $this->childBoardId,
            'permit' => $createPostPermitId,
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
            'table__recipient' => '0',
            'recipient' => (string) $this->userId,
            'permit' => (string) $showPermitId,
            'value' => '2',
        ]);

        $create->assertRedirect('/board/permissions/' . $this->childBoardId);
        $this->assertDatabaseHas('dra_permission', [
            'table__recipient' => 0,
            'recipient' => $this->userId,
            'table__subject' => 3,
            'subject' => $this->childBoardId,
            'permit' => $showPermitId,
            'value' => 2,
        ]);

        $editForm = $this->get('/permission/edit/' . $createPostPermissionId);

        $editForm->assertOk();
        $editForm->assertSee('Recht bearbeiten');
        $editForm->assertSee('editpermission', false);
        $editForm->assertSee('createpost');

        $edit = $this->post('/permission/edit/' . $createPostPermissionId, [
            'table__recipient' => '0',
            'recipient' => (string) $this->userId,
            'permit' => (string) $createPostPermitId,
            'value' => '1',
        ]);

        $edit->assertRedirect('/board/permissions/' . $this->childBoardId);
        $this->assertDatabaseHas('dra_permission', [
            'id' => $createPostPermissionId,
            'permit' => $createPostPermitId,
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
        $this->assertDatabaseMissing('dra_permission', [
            'id' => $createPostPermissionId,
        ]);
    }

    public function test_post_ip_page_shows_author_ips_and_other_users_with_same_ip(): void
    {
        $viewIpPermitId = (int) DB::table('dra_permit')->where('name', 'viewip')->value('id');
        $otherUserId = DB::table('dra_user')->insertGetId([
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

        DB::table('dra_post')->insert([
            'board' => $this->childBoardId,
            'thread' => $this->threadId,
            'user' => $this->userId,
            'character' => $this->characterId,
            'time' => $this->postTime + 1,
            'message' => $this->prefix . '_other_ip_message',
            'smilies' => 1,
            'signature' => 0,
            'ip' => '192.0.2.44',
        ]);

        DB::table('dra_post')->insert([
            'board' => $this->childBoardId,
            'thread' => $this->threadId,
            'user' => $otherUserId,
            'character' => $this->characterId,
            'time' => $this->postTime + 2,
            'message' => $this->prefix . '_same_ip_message',
            'smilies' => 1,
            'signature' => 0,
            'ip' => '127.0.0.1',
        ]);

        DB::table('dra_permission')->insert([
            'table__recipient' => 0,
            'recipient' => $this->userId,
            'table__subject' => 3,
            'subject' => $this->childBoardId,
            'permit' => $viewIpPermitId,
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
        $this->assertDatabaseHas('dra_thread', [
            'id' => $this->threadId,
            'post__total' => 2,
            'post__last' => $createdPost->id,
        ]);
        $this->assertDatabaseHas('dra_board', [
            'id' => $this->childBoardId,
            'post__total' => 2,
            'post__last' => $createdPost->id,
        ]);
        $this->assertDatabaseHas('dra_user', [
            'id' => $this->userId,
            'post__total' => 2,
        ]);
        $this->assertDatabaseHas('dra_character', [
            'id' => $this->characterId,
            'post__total' => 2,
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
        $this->assertDatabaseHas('dra_post', [
            'id' => $createdPost->id,
            'character' => $this->secondCharacterId,
            'message' => $this->prefix . '_edited_post',
        ]);
        $this->assertDatabaseHas('dra_character', [
            'id' => $this->characterId,
            'post__total' => 1,
        ]);
        $this->assertDatabaseHas('dra_character', [
            'id' => $this->secondCharacterId,
            'post__total' => 1,
        ]);

        $deletePage = $this->get('/post/delete/' . $createdPost->id);

        $deletePage->assertOk();
        $deletePage->assertSee('Beitrag löschen');
        $deletePage->assertSee($this->prefix . '_edited_post');

        $deleteResponse = $this->post('/post/delete/' . $createdPost->id, [
            'delete' => '1',
        ]);

        $deleteResponse->assertRedirect('/thread/view/' . $this->threadId);
        $this->assertDatabaseMissing('dra_post', [
            'id' => $createdPost->id,
        ]);
        $this->assertDatabaseHas('dra_thread', [
            'id' => $this->threadId,
            'post__total' => 1,
            'post__last' => $this->postId,
        ]);
        $this->assertDatabaseHas('dra_board', [
            'id' => $this->childBoardId,
            'post__total' => 1,
            'post__last' => $this->postId,
        ]);
        $this->assertDatabaseHas('dra_user', [
            'id' => $this->userId,
            'post__total' => 1,
        ]);
        $this->assertDatabaseHas('dra_character', [
            'id' => $this->secondCharacterId,
            'post__total' => 0,
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

        $newCharacterId = DB::table('dra_character')
            ->where('name', $this->prefix . '_inline_character')
            ->value('id');
        $createdPost = Post::where('message', $this->prefix . '_inline_character_post')->firstOrFail();

        $createResponse->assertRedirect('/thread/view/' . $this->threadId . '/last#post' . $createdPost->id);
        $this->assertDatabaseHas('dra_character', [
            'id' => $newCharacterId,
            'user' => $this->userId,
            'usertext' => '',
            'post__total' => 1,
        ]);
        $this->assertDatabaseHas('dra_post', [
            'id' => $createdPost->id,
            'character' => $newCharacterId,
            'user' => $this->userId,
            'message' => $this->prefix . '_inline_character_post',
        ]);
        $this->assertDatabaseHas('dra_thread', [
            'id' => $this->threadId,
            'post__total' => 2,
            'post__last' => $createdPost->id,
        ]);
        $this->assertDatabaseHas('dra_user', [
            'id' => $this->userId,
            'post__total' => 2,
        ]);
    }

    public function test_thread_view_rescues_legacy_inventory_transfer_form(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $itemId = DB::table('dra_item')->insertGetId([
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
        $inventoryId = DB::table('dra_inventory')->insertGetId([
            'item' => $itemId,
            'stack' => 3,
            'wear' => 0,
            'owner' => $this->characterId,
            'table__owner' => 6,
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

        $itemId = DB::table('dra_item')->insertGetId([
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
        $inventoryId = DB::table('dra_inventory')->insertGetId([
            'item' => $itemId,
            'stack' => 3,
            'wear' => 0,
            'owner' => $this->characterId,
            'table__owner' => 6,
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

        $postId = (int) DB::table('dra_post')
            ->where('thread', $this->threadId)
            ->where('character', 3)
            ->value('id');
        $transferId = (int) DB::table('dra_transfer')
            ->where('post', $postId)
            ->value('id');

        $response->assertRedirect('/thread/view/' . $this->threadId . '/last#post' . $postId);
        $this->assertDatabaseHas('dra_inventory', [
            'id' => $inventoryId,
            'owner' => $this->characterId,
            'table__owner' => 6,
            'stack' => 1,
        ]);
        $this->assertDatabaseHas('dra_inventory', [
            'item' => $itemId,
            'owner' => $this->secondCharacterId,
            'table__owner' => 6,
            'stack' => 2,
        ]);
        $this->assertDatabaseHas('dra_post', [
            'id' => $postId,
            'thread' => $this->threadId,
            'board' => $this->childBoardId,
            'user' => 2,
            'character' => 3,
            'message' => '',
        ]);
        $this->assertDatabaseHas('dra_transfer', [
            'id' => $transferId,
            'post' => $postId,
            'sender' => $this->characterId,
            'table__sender' => 6,
            'recipient' => $this->secondCharacterId,
            'table__recipient' => 6,
        ]);
        $this->assertDatabaseHas('dra_transferitem', [
            'transfer' => $transferId,
            'item' => $itemId,
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
        $this->assertDatabaseMissing('dra_post', [
            'id' => $this->postId,
        ]);
        $this->assertDatabaseMissing('dra_thread', [
            'id' => $this->threadId,
        ]);
        $this->assertDatabaseHas('dra_board', [
            'id' => $this->childBoardId,
            'thread__total' => 0,
            'post__total' => 0,
            'post__last' => 0,
        ]);
        $this->assertDatabaseHas('dra_user', [
            'id' => $this->userId,
            'post__total' => 0,
        ]);
        $this->assertDatabaseHas('dra_character', [
            'id' => $this->characterId,
            'post__total' => 0,
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
        $createdPost = Post::where('thread', $createdThread->id)->firstOrFail();

        $createResponse->assertRedirect('/thread/view/' . $createdThread->id);
        $this->assertDatabaseHas('dra_thread', [
            'id' => $createdThread->id,
            'board' => $this->childBoardId,
            'post__total' => 1,
            'post__first' => $createdPost->id,
            'post__last' => $createdPost->id,
            'important' => 1,
        ]);
        $this->assertDatabaseHas('dra_post', [
            'id' => $createdPost->id,
            'board' => $this->childBoardId,
            'thread' => $createdThread->id,
            'user' => $this->userId,
            'character' => $this->characterId,
            'message' => $this->prefix . '_created_thread_message',
        ]);
        $this->assertDatabaseHas('dra_board', [
            'id' => $this->childBoardId,
            'thread__total' => 2,
            'post__total' => 2,
            'post__last' => $createdPost->id,
        ]);
        $this->assertDatabaseHas('dra_user', [
            'id' => $this->userId,
            'post__total' => 2,
        ]);
        $this->assertDatabaseHas('dra_character', [
            'id' => $this->characterId,
            'post__total' => 2,
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
        $this->assertDatabaseHas('dra_thread', [
            'id' => $createdThread->id,
            'board' => $this->otherBoardId,
            'name' => $this->prefix . '_moved_thread',
            'important' => 0,
        ]);
        $this->assertDatabaseHas('dra_post', [
            'id' => $createdPost->id,
            'board' => $this->otherBoardId,
            'thread' => $createdThread->id,
        ]);
        $this->assertDatabaseHas('dra_board', [
            'id' => $this->childBoardId,
            'thread__total' => 1,
            'post__total' => 1,
            'post__last' => $this->postId,
        ]);
        $this->assertDatabaseHas('dra_board', [
            'id' => $this->otherBoardId,
            'thread__total' => 1,
            'post__total' => 1,
            'post__last' => $createdPost->id,
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
        $this->assertDatabaseMissing('dra_thread', [
            'id' => $createdThread->id,
        ]);
        $this->assertDatabaseMissing('dra_post', [
            'id' => $createdPost->id,
        ]);
        $this->assertDatabaseHas('dra_board', [
            'id' => $this->otherBoardId,
            'thread__total' => 0,
            'post__total' => 0,
            'post__last' => 0,
        ]);
        $this->assertDatabaseHas('dra_user', [
            'id' => $this->userId,
            'post__total' => 1,
        ]);
        $this->assertDatabaseHas('dra_character', [
            'id' => $this->characterId,
            'post__total' => 1,
        ]);
    }

    private function createExtraThreads(int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            DB::table('dra_thread')->insert([
                'board' => $this->childBoardId,
                'name' => $this->prefix . '_extra_thread_' . $i,
                'post__first_time' => $this->postTime - $i,
                'post__last_time' => $this->postTime - $i,
                'post__total' => 0,
            ]);
        }

        DB::table('dra_board')
            ->where('id', $this->childBoardId)
            ->update([
                'thread__total' => $count + 1,
            ]);
    }

    private function createExtraPosts(int $count): void
    {
        $lastPostId = $this->postId;

        for ($i = 1; $i <= $count; $i++) {
            $lastPostId = DB::table('dra_post')->insertGetId([
                'board' => $this->childBoardId,
                'thread' => $this->threadId,
                'user' => $this->userId,
                'character' => $this->characterId,
                'time' => $this->postTime + $i,
                'message' => $this->prefix . '_extra_message_' . $i,
                'smilies' => 1,
                'signature' => 0,
                'ip' => '127.0.0.1',
            ]);
        }

        DB::table('dra_thread')
            ->where('id', $this->threadId)
            ->update([
                'post__total' => $count + 1,
                'post__last' => $lastPostId,
                'post__last_time' => $this->postTime + $count,
            ]);
    }
}
