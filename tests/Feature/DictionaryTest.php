<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DictionaryTest extends TestCase
{
    private string $prefix;
    private int $languageFromId;
    private int $languageToId;
    private int $wordTypeId;
    private int $wordFromId;
    private int $wordToId;
    private int $keyId;
    private array $originalPermitStandards = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->prefix = 'codex_dictionary_test_' . str_replace('.', '_', uniqid('', true));

        $this->rememberPermitStandards([
            'createdictionary',
            'editdictionary',
            'deletedictionary',
        ]);

        $this->languageFromId = DB::table('languages')->insertGetId([
            'name' => $this->prefix . '_from',
            'code' => 'xf',
        ]);
        $this->languageToId = DB::table('languages')->insertGetId([
            'name' => $this->prefix . '_to',
            'code' => 'xt',
        ]);
        $this->wordTypeId = DB::table('word_types')->insertGetId([
            'name' => $this->prefix . '_type',
            'code' => 'xwt',
        ]);
        $this->wordFromId = DB::table('words')->insertGetId([
            'language_id' => $this->languageFromId,
            'word_type_id' => $this->wordTypeId,
            'word' => $this->prefix . '_from_word',
            'val' => 0,
        ]);
        $this->wordToId = DB::table('words')->insertGetId([
            'language_id' => $this->languageToId,
            'word_type_id' => $this->wordTypeId,
            'word' => $this->prefix . '_to_word',
            'val' => 0,
        ]);
        $this->keyId = DB::table('keys')->insertGetId([
            'from_word_id' => $this->wordFromId,
            'to_word_id' => $this->wordToId,
        ]);

        Cache::flush();
    }

    protected function tearDown(): void
    {
        DB::table('keys')
            ->whereIn('from_word_id', $this->fixtureWordIds())
            ->orWhereIn('to_word_id', $this->fixtureWordIds())
            ->delete();

        DB::table('words')
            ->where('word', 'like', $this->prefix . '%')
            ->delete();

        DB::table('languages')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        DB::table('word_types')
            ->where('name', 'like', $this->prefix . '%')
            ->delete();

        foreach ($this->originalPermitStandards as $name => $standard) {
            DB::table('permits')
                ->where('name', $name)
                ->update(['standard' => $standard]);
        }

        Cache::flush();

        parent::tearDown();
    }

    public function test_dictionary_index_renders_words_and_translations(): void
    {
        $response = $this->get('/dictionary/viewall/' . $this->languageFromId . '/' . $this->languageToId . '/word,a');

        $response->assertOk();
        $response->assertSee('Diktionar');
        $response->assertSee($this->prefix . '_from_word');
        $response->assertSee($this->prefix . '_to_word');
    }

    public function test_dictionary_word_page_renders_translations(): void
    {
        $response = $this->get('/dictionary/view/' . $this->wordFromId);

        $response->assertOk();
        $response->assertSee('Wort:');
        $response->assertSee($this->prefix . '_from_word');
        $response->assertSee($this->prefix . '_to_word');
    }

    public function test_ajax_word_search_returns_legacy_tokeninput_shape(): void
    {
        $response = $this->post('/dictionary/ajax__getwords', [
            'q' => $this->prefix . '_from',
        ]);

        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $this->wordFromId,
            'word' => $this->prefix . '_from_word',
            'wordtype' => 'xwt',
            'language' => $this->languageFromId,
        ]);
    }

    public function test_dictionary_create_requires_permission(): void
    {
        $this->setPermitStandard('createdictionary', 0);

        $response = $this->get('/dictionary/create');

        $response->assertForbidden();
    }

    public function test_dictionary_create_stores_word_when_permission_allows_it(): void
    {
        $this->setPermitStandard('createdictionary', 2);

        $word = $this->prefix . '_created_word';

        $response = $this->post('/dictionary/create', [
            'language_id' => $this->languageFromId,
            'word_type_id' => $this->wordTypeId,
            'word' => $word,
        ]);

        $createdId = DB::table('words')->where('word', $word)->value('id');

        $response->assertRedirect('/dictionary/view/' . $createdId);
        $this->assertDatabaseHas('words', [
            'id' => $createdId,
            'language_id' => $this->languageFromId,
            'word_type_id' => $this->wordTypeId,
            'word' => $word,
        ]);
    }

    public function test_dictionary_edit_updates_word_when_permission_allows_it(): void
    {
        $this->setPermitStandard('editdictionary', 2);

        $response = $this->post('/dictionary/edit/' . $this->wordFromId, [
            'language_id' => $this->languageFromId,
            'word_type_id' => $this->wordTypeId,
            'word' => $this->prefix . '_edited_word',
        ]);

        $response->assertRedirect('/dictionary/view/' . $this->wordFromId);
        $this->assertDatabaseHas('words', [
            'id' => $this->wordFromId,
            'word' => $this->prefix . '_edited_word',
        ]);
    }

    public function test_dictionary_key_can_be_created_and_deleted_when_permission_allows_it(): void
    {
        $this->setPermitStandard('createdictionary', 2);
        $this->setPermitStandard('deletedictionary', 2);

        DB::table('keys')->where('id', $this->keyId)->delete();

        $createResponse = $this->post('/dictionary/createkey/' . $this->wordFromId, [
            'word' => (string)$this->wordToId,
            'bijective' => '1',
        ]);

        $createdKey = DB::table('keys')
            ->where('from_word_id', $this->wordFromId)
            ->where('to_word_id', $this->wordToId)
            ->first();

        $createResponse->assertRedirect('/dictionary/view/' . $this->wordFromId);
        $this->assertNotNull($createdKey);
        $this->assertDatabaseHas('keys', [
            'from_word_id' => $this->wordToId,
            'to_word_id' => $this->wordFromId,
        ]);

        $deleteResponse = $this->post('/dictionary/deletekey/' . $createdKey->id, [
            'delete' => '1',
        ]);

        $deleteResponse->assertRedirect('/dictionary/view/' . $this->wordFromId);
        $this->assertDatabaseMissing('keys', [
            'id' => $createdKey->id,
        ]);
    }

    public function test_create_key_page_offers_html_search_fallback(): void
    {
        $this->setPermitStandard('createdictionary', 2);

        $response = $this->get('/dictionary/createkey/' . $this->wordFromId . '?q=' . $this->prefix . '_to');

        $response->assertOk();
        $response->assertSee('Übersetzung verknüpfen');
        $response->assertSee('Wort suchen');
        $response->assertSee($this->prefix . '_to_word');
        $response->assertSee('Einfach verknüpfen');
        $response->assertSee('Bijektiv verknüpfen');
        $response->assertSee('name="word" value="' . $this->wordToId . '"', false);
        $response->assertDontSee('Zu verknüpfende Wort-ID');
        $response->assertDontSee('value="Neue Verknüpfung erstellen"', false);
    }

    public function test_create_key_page_includes_alpine_autocomplete_enhancement(): void
    {
        $this->setPermitStandard('createdictionary', 2);

        $response = $this->get('/dictionary/createkey/' . $this->wordFromId);

        $response->assertOk();
        $response->assertSee('x-data="dictionaryKeySearch', false);
        $response->assertSee('x-on:submit="submitSearch"', false);
        $response->assertSee('x-on:input.debounce.300ms="search"', false);
        $response->assertSee('js/alpine.min.js', false);
        $response->assertDontSee('unpkg.com/alpinejs', false);
    }

    public function test_create_key_search_partial_returns_result_markup_without_layout(): void
    {
        $this->setPermitStandard('createdictionary', 2);

        $response = $this
            ->withHeader('X-Dictionary-Search', 'partial')
            ->get('/dictionary/createkey/' . $this->wordFromId . '?q=' . $this->prefix . '_to');

        $response->assertOk();
        $response->assertSee($this->prefix . '_to_word');
        $response->assertSee('Einfach verknüpfen');
        $response->assertDontSee('<html', false);
        $response->assertDontSee('x-data="dictionaryKeySearch', false);
    }

    public function test_dictionary_delete_removes_word_and_translation_keys_when_permission_allows_it(): void
    {
        $this->setPermitStandard('deletedictionary', 2);

        $response = $this->post('/dictionary/delete/' . $this->wordFromId, [
            'delete' => '1',
        ]);

        $response->assertRedirect('/dictionary');
        $this->assertDatabaseMissing('words', [
            'id' => $this->wordFromId,
        ]);
        $this->assertDatabaseMissing('keys', [
            'id' => $this->keyId,
        ]);
    }

    private function rememberPermitStandards(array $permitNames): void
    {
        $this->originalPermitStandards = DB::table('permits')
            ->whereIn('name', $permitNames)
            ->pluck('standard', 'name')
            ->all();
    }

    private function setPermitStandard(string $name, int $standard): void
    {
        DB::table('permits')
            ->where('name', $name)
            ->update(['standard' => $standard]);

        Cache::flush();
    }

    private function fixtureWordIds(): array
    {
        return array_filter([
            $this->wordFromId ?? null,
            $this->wordToId ?? null,
        ]);
    }
}
