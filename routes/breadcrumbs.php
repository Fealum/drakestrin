<?php // routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.

use App\Models\Page;
use App\Models\User;
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
