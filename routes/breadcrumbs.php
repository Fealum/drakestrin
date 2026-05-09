<?php // routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.

use App\Models\Page;
use App\Models\User;
use App\Models\Dictionary\Key;
use App\Models\Dictionary\Word;
use Diglactic\Breadcrumbs\Breadcrumbs;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Index
Breadcrumbs::for('index', function (BreadcrumbTrail $trail) {
    $trail->push(config('app.name'), route('index'));
});

Breadcrumbs::for('calendar', function (BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Kalendarium', route('calendar'));
});

Breadcrumbs::for('conversation', function (BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Konversationen', route('conversation'));
});

Breadcrumbs::for('conversation.view', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('conversation');
    $trail->push('Konversation mit ' . $user->name, route('conversation.view', $user));
});

Breadcrumbs::for('encyclopedia', function (BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Kompendium', route('encyclopedia'));
});

Breadcrumbs::for('encyclopedia.view', function (BreadcrumbTrail $trail, Page $page) {
    if ($page->parent) {
        $trail->parent('encyclopedia.view', $page->parent);
    } else {
        $trail->parent('encyclopedia');
    }

    $trail->push($page->name, route('encyclopedia.view', $page->id));
});

Breadcrumbs::for('encyclopedia.create', function (BreadcrumbTrail $trail, Page $page) {
    $trail->parent('encyclopedia.view', $page);
    $trail->push('Unterseite anlegen', route('encyclopedia.create', $page->id));
});

Breadcrumbs::for('encyclopedia.edit', function (BreadcrumbTrail $trail, Page $page) {
    $trail->parent('encyclopedia.view', $page);
    $trail->push('Seite bearbeiten', route('encyclopedia.edit', $page->id));
});

Breadcrumbs::for('encyclopedia.delete', function (BreadcrumbTrail $trail, Page $page) {
    $trail->parent('encyclopedia.view', $page);
    $trail->push('Seite löschen', route('encyclopedia.delete', $page->id));
});

Breadcrumbs::for('dictionary.index', function (BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Diktionar', route('dictionary.index'));
});

Breadcrumbs::for('dictionary.viewall', function (BreadcrumbTrail $trail) {
    $trail->parent('dictionary.index');
});

Breadcrumbs::for('dictionary.view', function (BreadcrumbTrail $trail, Word $word) {
    $trail->parent('dictionary.index');
    $trail->push($word->word, route('dictionary.view', $word->id));
});

Breadcrumbs::for('dictionary.create', function (BreadcrumbTrail $trail) {
    $trail->parent('dictionary.index');
    $trail->push('Neues Wort erstellen', route('dictionary.create'));
});

Breadcrumbs::for('dictionary.edit', function (BreadcrumbTrail $trail, Word $word) {
    $trail->parent('dictionary.view', $word);
    $trail->push('Wort bearbeiten', route('dictionary.edit', $word->id));
});

Breadcrumbs::for('dictionary.delete', function (BreadcrumbTrail $trail, Word $word) {
    $trail->parent('dictionary.view', $word);
    $trail->push('Wort löschen', route('dictionary.delete', $word->id));
});

Breadcrumbs::for('dictionary.create_key', function (BreadcrumbTrail $trail, Word $word) {
    $trail->parent('dictionary.view', $word);
    $trail->push('Übersetzung verknüpfen', route('dictionary.create_key', $word->id));
});

Breadcrumbs::for('dictionary.delete_key', function (BreadcrumbTrail $trail, Key $key) {
    $trail->parent('dictionary.view', $key->fromWord);
    $trail->push('Verknüpfung löschen', route('dictionary.delete_key', $key->id));
});
