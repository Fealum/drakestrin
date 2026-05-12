<?php // routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.

use App\Models\Page;
use App\Models\Permission;
use App\Models\User;
use App\Models\Character;
use App\Models\Company;
use App\Models\CompanyWorker;
use App\Models\Group;
use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread;
use App\Models\Dictionary\Key;
use App\Models\Dictionary\Word;
use App\Models\Territory\Territory;
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

Breadcrumbs::for('user', function (BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Mitglieder', route('user'));
});

Breadcrumbs::for('user.viewall', function (BreadcrumbTrail $trail) {
    $trail->parent('user');
});

Breadcrumbs::for('user.view', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('user');
    $trail->push($user->name, route('user.view', $user->id));
});

Breadcrumbs::for('user.edit', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('user.view', $user);
    $trail->push('Nutzer bearbeiten', route('user.edit', $user->id));
});

Breadcrumbs::for('user.character', function (BreadcrumbTrail $trail, Character $character) {
    if ($character->userModel) {
        $trail->parent('user.view', $character->userModel);
    } else {
        $trail->parent('user');
    }

    $trail->push($character->name, route('user.character', $character->id));
});

Breadcrumbs::for('user.create_character', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('user.view', $user);
    $trail->push('Neuen Charakter erstellen', route('user.create_character', $user->id));
});

Breadcrumbs::for('user.edit_character', function (BreadcrumbTrail $trail, Character $character) {
    $trail->parent('user.character', $character);
    $trail->push('Charakter bearbeiten', route('user.edit_character', $character->id));
});

Breadcrumbs::for('user.create_contact', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('user.view', $user);
    $trail->push('Neue Kontaktmöglichkeit erstellen', route('user.create_contact', $user->id));
});

Breadcrumbs::for('user.edit_contact', function (BreadcrumbTrail $trail, \App\Models\UserContact $contact) {
    if ($contact->userModel) {
        $trail->parent('user.view', $contact->userModel);
    } else {
        $trail->parent('user');
    }

    $trail->push('Kontaktmöglichkeit bearbeiten', route('user.edit_contact', $contact->id));
});

Breadcrumbs::for('user.delete_contact', function (BreadcrumbTrail $trail, \App\Models\UserContact $contact) {
    if ($contact->userModel) {
        $trail->parent('user.view', $contact->userModel);
    } else {
        $trail->parent('user');
    }

    $trail->push('Kontaktmöglichkeit löschen', route('user.delete_contact', $contact->id));
});

Breadcrumbs::for('group', function (BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Gruppen', route('group'));
});

Breadcrumbs::for('group.view', function (BreadcrumbTrail $trail, Group $group) {
    $trail->parent('group');
    $trail->push($group->name, route('group.view', $group->id));
});

Breadcrumbs::for('company', function (BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Kontor', route('company'));
});

Breadcrumbs::for('company.viewall', function (BreadcrumbTrail $trail) {
    $trail->parent('company');
});

Breadcrumbs::for('company.view', function (BreadcrumbTrail $trail, Company $company) {
    $trail->parent('company');
    $trail->push($company->name, route('company.view', $company->id));
});

Breadcrumbs::for('company.worker', function (BreadcrumbTrail $trail, CompanyWorker $worker) {
    if ($worker->companyModel) {
        $trail->parent('company.view', $worker->companyModel);
    } else {
        $trail->parent('company');
    }

    $trail->push($worker->name, route('company.worker', $worker->id));
});

Breadcrumbs::for('board', function (BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Forum', route('board'));
});

Breadcrumbs::for('board.filter', function (BreadcrumbTrail $trail) {
    $trail->parent('board');
});

Breadcrumbs::for('board.view', function (BreadcrumbTrail $trail, Board $board) {
    if ($board->parentBoard) {
        $trail->parent('board.view', $board->parentBoard);
    } else {
        $trail->parent('board');
    }

    $trail->push($board->name, route('board.view', $board->id));
});

Breadcrumbs::for('board.view.legacy', function (BreadcrumbTrail $trail, Board $board) {
    $trail->parent('board.view', $board);
});

Breadcrumbs::for('board.permissions', function (BreadcrumbTrail $trail, Board $board) {
    $trail->parent('board.view', $board);
    $trail->push('Rechte', route('board.permissions', $board->id));
});

Breadcrumbs::for('permission.create_board', function (BreadcrumbTrail $trail, Board $board) {
    $trail->parent('board.permissions', $board);
    $trail->push('Neues Recht', route('permission.create_board', $board->id));
});

Breadcrumbs::for('permission.edit', function (BreadcrumbTrail $trail, Permission $permission) {
    if ((int) $permission->table__subject === 3 && $permission->subject_legacy) {
        $trail->parent('board.permissions', $permission->subject_legacy);
    } else {
        $trail->parent('board');
    }

    $trail->push('Recht bearbeiten', route('permission.edit', $permission->id));
});

Breadcrumbs::for('permission.delete', function (BreadcrumbTrail $trail, Permission $permission) {
    if ((int) $permission->table__subject === 3 && $permission->subject_legacy) {
        $trail->parent('board.permissions', $permission->subject_legacy);
    } else {
        $trail->parent('board');
    }

    $trail->push('Recht löschen', route('permission.delete', $permission->id));
});

Breadcrumbs::for('thread.view', function (BreadcrumbTrail $trail, Thread $thread) {
    if ($thread->boardModel) {
        $trail->parent('board.view', $thread->boardModel);
    } else {
        $trail->parent('board');
    }

    $trail->push($thread->name, route('thread.view', $thread->id));
});

Breadcrumbs::for('thread.create', function (BreadcrumbTrail $trail, $board = null) {
    if ($board instanceof Board) {
        $trail->parent('board.view', $board);
    } else {
        $trail->parent('board');
    }

    $trail->push('Neues Thema erstellen', route('thread.create', $board instanceof Board ? ['board' => $board->id] : []));
});

Breadcrumbs::for('thread.edit', function (BreadcrumbTrail $trail, Thread $thread) {
    $trail->parent('thread.view', $thread);
    $trail->push('Thema bearbeiten', route('thread.edit', $thread->id));
});

Breadcrumbs::for('thread.delete', function (BreadcrumbTrail $trail, Thread $thread) {
    $trail->parent('thread.view', $thread);
    $trail->push('Thema löschen', route('thread.delete', $thread->id));
});

Breadcrumbs::for('post.edit', function (BreadcrumbTrail $trail, Post $post) {
    $trail->parent('thread.view', $post->threadModel);
    $trail->push('Beitrag bearbeiten', route('post.edit', $post->id));
});

Breadcrumbs::for('post.delete', function (BreadcrumbTrail $trail, Post $post) {
    $trail->parent('thread.view', $post->threadModel);
    $trail->push('Beitrag löschen', route('post.delete', $post->id));
});

Breadcrumbs::for('post.ip', function (BreadcrumbTrail $trail, Post $post) {
    $trail->parent('thread.view', $post->threadModel);
    $trail->push('IP-Adresse', route('post.ip', $post->id));
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

Breadcrumbs::for('dictionary', function (BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Diktionar', route('dictionary'));
});

Breadcrumbs::for('dictionary.viewall', function (BreadcrumbTrail $trail) {
    $trail->parent('dictionary');
});

Breadcrumbs::for('dictionary.view', function (BreadcrumbTrail $trail, Word $word) {
    $trail->parent('dictionary');
    $trail->push($word->word, route('dictionary.view', $word->id));
});

Breadcrumbs::for('dictionary.create', function (BreadcrumbTrail $trail) {
    $trail->parent('dictionary');
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

Breadcrumbs::for('territory', function (BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Atlas', route('territory'));
});

Breadcrumbs::for('territory.view', function (BreadcrumbTrail $trail, Territory $territory) {
    if ($territory->parent && $territory->parent->id !== 1) {
        $trail->parent('territory.view', $territory->parent);
    } else {
        $trail->parent('territory');
    }

    if ($territory->id !== 1) {
        $trail->push($territory->name, route('territory.view', $territory->id));
    }
});
