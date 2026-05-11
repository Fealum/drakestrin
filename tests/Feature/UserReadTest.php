<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserReadTest extends TestCase
{
    private const TEST_PROTOCOL_ID = 120;

    private string $prefix;
    private int $viewerId;
    private int $userId;
    private int $characterId;
    private int $protocolId;
    private int $contactId;
    private int $timestamp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prefix = '000_ct_user_' . substr(str_replace('.', '_', uniqid('', true)), 0, 8);
        $this->timestamp = now()->subDays(10)->timestamp;

        $this->viewerId = DB::table('dra_user')->insertGetId([
            'name' => $this->prefix . '_viewer',
            'password' => 'secret',
            'email' => $this->prefix . '_viewer@example.test',
            'regemail' => $this->prefix . '_viewer@example.test',
            'regdate' => $this->timestamp,
            'lastvisit' => $this->timestamp,
            'lastactivity' => $this->timestamp,
            'interests' => '',
            'location' => '',
            'work' => '',
            'usertext' => '',
            'wohnort' => '',
        ]);

        $this->userId = DB::table('dra_user')->insertGetId([
            'name' => $this->prefix . '_member',
            'password' => 'secret',
            'email' => $this->prefix . '_member@example.test',
            'regemail' => $this->prefix . '_member@example.test',
            'regdate' => $this->timestamp,
            'lastvisit' => $this->timestamp,
            'lastactivity' => $this->timestamp,
            'birthday' => 1024,
            'interests' => $this->prefix . '_interests',
            'location' => $this->prefix . '_location',
            'work' => $this->prefix . '_work',
            'usertext' => $this->prefix . "_profile\ntext",
            'gender' => 1,
            'post__total' => 12,
            'wohnort' => '',
        ]);

        $this->characterId = DB::table('dra_character')->insertGetId([
            'name' => $this->prefix . '_character',
            'post__total' => 7,
            'regdate' => $this->timestamp,
            'birthday' => 2048,
            'interests' => $this->prefix . '_char_interests',
            'location' => $this->prefix . '_char_location',
            'work' => $this->prefix . '_char_work',
            'gender' => 2,
            'usertext' => $this->prefix . '_char_text',
            'user' => $this->userId,
        ]);

        DB::table('dra_group2user')->insert([
            'user' => $this->userId,
            'group' => 2,
        ]);

        $this->protocolId = self::TEST_PROTOCOL_ID;
        DB::table('dra_protocol')->updateOrInsert(['id' => $this->protocolId], [
            'name' => 'Proto',
            'format' => '',
            'link' => 'https://example.test/\1',
        ]);

        $this->contactId = DB::table('dra_user_contact')->insertGetId([
            'user' => $this->userId,
            'protocol' => $this->protocolId,
            'contact' => $this->prefix . '_contact',
        ]);
    }

    protected function tearDown(): void
    {
        DB::table('dra_user_contact')
            ->where('id', $this->contactId)
            ->orWhere('user', $this->userId)
            ->delete();
        DB::table('dra_protocol')->where('id', $this->protocolId)->delete();
        DB::table('dra_permission')->whereIn('recipient', [$this->viewerId, $this->userId])->delete();
        DB::table('dra_group2user')->where('user', $this->userId)->delete();
        DB::table('dra_character')->where('name', 'like', $this->prefix . '%')->delete();
        DB::table('dra_online')->whereIn('user', [$this->viewerId, $this->userId])->delete();
        DB::table('dra_user')->where('name', 'like', $this->prefix . '%')->delete();

        parent::tearDown();
    }

    public function test_user_list_renders_legacy_member_overview_shape(): void
    {
        $response = $this->get('/user/viewall/name,a');

        $response->assertOk();
        $response->assertSee('Mitglieder');
        $response->assertSee('Sortieren');
        $response->assertSee('/user/viewall/postsperday,d;name,d', false);
        $response->assertSee($this->prefix . '_member');
        $response->assertSee('/user/view/' . $this->userId, false);
        $response->assertSee('/user/character/' . $this->characterId, false);
        $response->assertSee('/board/filter/user_contains:' . $this->userId, false);
        $response->assertSee('/board/filter/char_contains:' . $this->characterId, false);
        $response->assertDontSee('?page=', false);
    }

    public function test_user_profile_renders_read_only_legacy_links_and_contact_data(): void
    {
        $this->actingAs(User::findOrFail($this->viewerId));

        $response = $this->get('/user/view/' . $this->userId);

        $response->assertOk();
        $response->assertSee($this->prefix . '_profile');
        $response->assertSee('Aktivität');
        $response->assertSee('Gruppen');
        $response->assertSee('/group/view/2', false);
        $response->assertSee('/board/filter/user_contains:' . $this->userId, false);
        $response->assertSee('/user/character/' . $this->characterId, false);
        $response->assertSee('Proto');
        $response->assertSee('https://example.test/' . $this->prefix . '_contact', false);
        $response->assertSee('/conversation/view/' . $this->userId, false);
    }

    public function test_group_profile_renders_legacy_member_list_shape(): void
    {
        $response = $this->get('/group/view/2');

        $response->assertOk();
        $response->assertSee('Gruppe');
        $response->assertSee('Mitglieder');
        $response->assertSee($this->prefix . '_member');
        $response->assertSee('/user/view/' . $this->userId, false);
        $response->assertSee('/user/character/' . $this->characterId, false);
        $response->assertDontSee('?page=', false);
    }

    public function test_character_profile_renders_parent_user_and_profile_information(): void
    {
        $response = $this->get('/user/character/' . $this->characterId);

        $response->assertOk();
        $response->assertSee($this->prefix . '_character');
        $response->assertSee('Übergeordneter Nutzer');
        $response->assertSee('/user/view/' . $this->userId, false);
        $response->assertSee('/board/filter/char_contains:' . $this->characterId, false);
        $response->assertSee($this->prefix . '_char_interests');
        $response->assertSee($this->prefix . '_char_work');
    }

    public function test_character_owner_sees_edit_link_without_explicit_permission(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $response = $this->get('/user/character/' . $this->characterId);

        $response->assertOk();
        $response->assertSee('/user/editcharacter/' . $this->characterId, false);

        $edit = $this->get('/user/editcharacter/' . $this->characterId);

        $edit->assertOk();
        $edit->assertSee('Avatar nicht ändern');
    }

    public function test_character_can_be_created_from_legacy_user_route(): void
    {
        $this->grantPermit($this->userId, 'createcharacter');
        $this->actingAs(User::findOrFail($this->userId));

        $form = $this->get('/user/createcharacter/' . $this->userId);
        $form->assertOk();
        $form->assertSee('Neuen Charakter erstellen');

        $response = $this->post('/user/createcharacter/' . $this->userId, [
            'name' => $this->prefix . '_new_character',
        ]);

        $characterId = DB::table('dra_character')
            ->where('name', $this->prefix . '_new_character')
            ->value('id');

        $this->assertNotNull($characterId);
        $response->assertRedirect('/user/editcharacter/' . $characterId);
        $this->assertDatabaseHas('dra_character', [
            'id' => $characterId,
            'user' => $this->userId,
            'usertext' => '',
        ]);
    }

    public function test_user_owner_can_edit_profile_and_select_character_avatar(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $form = $this->get('/user/edit/' . $this->userId);
        $form->assertOk();
        $form->assertSee('Avatar des folgenden Charakters als Nutzeravatar benutzen');

        $response = $this->post('/user/edit/' . $this->userId, [
            'character__avatar' => $this->characterId,
            'usertext' => $this->prefix . '_updated_usertext',
            'gender' => 3,
            'birthday' => 2999,
            'location' => $this->prefix . '_updated_user_location',
            'interests' => $this->prefix . '_updated_user_interests',
            'work' => $this->prefix . '_updated_user_work',
            'edit' => 1,
        ]);

        $response->assertRedirect('/user/view/' . $this->userId);
        $this->assertDatabaseHas('dra_user', [
            'id' => $this->userId,
            'character__avatar' => $this->characterId,
            'usertext' => $this->prefix . '_updated_usertext',
            'gender' => 3,
            'birthday' => 2999,
            'location' => $this->prefix . '_updated_user_location',
            'interests' => $this->prefix . '_updated_user_interests',
            'work' => $this->prefix . '_updated_user_work',
        ]);
    }

    public function test_user_owner_can_create_edit_and_delete_contact(): void
    {
        $this->actingAs(User::findOrFail($this->userId));

        $createForm = $this->get('/user/createcontact/' . $this->userId);
        $createForm->assertOk();
        $createForm->assertSee('Neue Kontaktmöglichkeit erstellen');

        $create = $this->post('/user/createcontact/' . $this->userId, [
            'protocol' => $this->protocolId,
            'contact' => $this->prefix . '_new_contact',
        ]);
        $create->assertRedirect('/user/view/' . $this->userId);

        $contactId = DB::table('dra_user_contact')
            ->where('user', $this->userId)
            ->where('contact', $this->prefix . '_new_contact')
            ->value('id');

        $this->assertNotNull($contactId);

        $editForm = $this->get('/user/editcontact/' . $contactId);
        $editForm->assertOk();
        $editForm->assertSee('Kontaktmöglichkeit bearbeiten');

        $edit = $this->post('/user/editcontact/' . $contactId, [
            'protocol' => $this->protocolId,
            'contact' => $this->prefix . '_changed_contact',
        ]);
        $edit->assertRedirect('/user/view/' . $this->userId);
        $this->assertDatabaseHas('dra_user_contact', [
            'id' => $contactId,
            'contact' => $this->prefix . '_changed_contact',
        ]);

        $deleteForm = $this->get('/user/deletecontact/' . $contactId);
        $deleteForm->assertOk();
        $deleteForm->assertSee('Kontaktmöglichkeit löschen');

        $delete = $this->post('/user/deletecontact/' . $contactId, [
            'delete' => 1,
        ]);
        $delete->assertRedirect('/user/view/' . $this->userId);
        $this->assertDatabaseMissing('dra_user_contact', [
            'id' => $contactId,
        ]);
    }

    public function test_character_can_be_edited_and_avatar_is_stored_on_public_disk(): void
    {
        Storage::fake('public');
        $this->grantPermit($this->userId, 'editcharacter');
        $this->actingAs(User::findOrFail($this->userId));

        $form = $this->get('/user/editcharacter/' . $this->characterId);
        $form->assertOk();
        $form->assertSee('Avatar nicht ändern');
        $form->assertSee('Nutzerkonto');

        $response = $this->post('/user/editcharacter/' . $this->characterId, [
            'usertext' => $this->prefix . '_updated_text',
            'gender' => 1,
            'birthday' => 3001,
            'location' => $this->prefix . '_updated_location',
            'interests' => $this->prefix . '_updated_interests',
            'work' => $this->prefix . '_updated_work',
            'changeavatar' => 1,
            'avatar' => UploadedFile::fake()->create('avatar.jpg', 10, 'image/jpeg'),
        ]);

        $response->assertRedirect('/user/character/' . $this->characterId);
        $this->assertDatabaseHas('dra_character', [
            'id' => $this->characterId,
            'usertext' => $this->prefix . '_updated_text',
            'gender' => 1,
            'birthday' => 3001,
            'location' => $this->prefix . '_updated_location',
            'interests' => $this->prefix . '_updated_interests',
            'work' => $this->prefix . '_updated_work',
            'avatar' => 1,
        ]);
        Storage::disk('public')->assertExists('character-avatars/' . $this->characterId . '.jpg');
        Storage::disk('public')->assertExists('character-avatars/thumb/' . $this->characterId . '.jpg');
    }

    private function grantPermit(int $userId, string $permitName, int $value = 1): void
    {
        DB::table('dra_permission')->insert([
            'table__recipient' => 0,
            'recipient' => $userId,
            'table__subject' => 0,
            'subject' => 0,
            'permit' => DB::table('dra_permit')->where('name', $permitName)->value('id'),
            'value' => $value,
        ]);

        Cache::forget('user_permissions:' . $userId);
        Cache::forget('user_permits:' . $userId);
    }
}
