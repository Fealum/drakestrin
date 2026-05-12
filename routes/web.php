<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\EncyclopediaController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\TerritoryController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LegacyController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(IndexController::class)->group(function () {
    Route::get('/', 'index')->name('index');
});

Route::get('/calendar', [CalendarController::class, 'view'])->name('calendar');

Route::controller(BoardController::class)->group(function () {
    Route::get('/board', 'index')->name('board');
    Route::get('/board/view/{board}/{page?}', function (int|string $board, int|string|null $page = null) {
        return redirect('/board/filter/board:' . $board . ($page && (int) $page > 1 ? '/' . $page : ''), 301);
    })->name('board.view.legacy');
    Route::get('/board/permissions/{board}', 'permissions')->name('board.permissions');
    Route::get('/board/filter/board:{board}/{page?}', 'filterBoard')->name('board.view');
    Route::get('/board/filter/{filter?}/{page?}', 'filter')->name('board.filter');
    Route::post('/board/setfilter', 'filterRedirect')->name('board.setfilter');
    Route::get('/board/ajax__getusers', 'ajaxGetUsers')->name('board.ajax_get_users');
    Route::get('/board/ajax__getchars', 'ajaxGetCharacters')->name('board.ajax_get_chars');
    Route::get('/board/changeshow/{board}/{change?}/{ajax?}', 'changeShow')->name('board.change_show');
});

Route::controller(PermissionController::class)->group(function () {
    Route::match(['get', 'post'], '/permission/create/board/{board}', 'createForBoard')->name('permission.create_board');
    Route::match(['get', 'post'], '/permission/edit/{permission}', 'edit')->name('permission.edit');
    Route::match(['get', 'post'], '/permission/delete/{permission}', 'delete')->name('permission.delete');
});

Route::controller(ThreadController::class)->group(function () {
    Route::match(['get', 'post'], '/thread/create/{board?}', 'create')->name('thread.create');
    Route::get('/thread/view/{thread}/{page?}', 'view')->name('thread.view');
    Route::match(['get', 'post'], '/thread/edit/{thread}', 'edit')->name('thread.edit');
    Route::get('/thread/delete/{thread}', 'delete')->name('thread.delete');
    Route::post('/thread/delete/{thread}', 'destroy')->name('thread.destroy');
});

Route::controller(PostController::class)->group(function () {
    Route::post('/post/create/{thread}', 'create')->name('post.create');
    Route::get('/post/view/{post}', 'view')->name('post.view');
    Route::get('/post/edit/{post}', 'edit')->name('post.edit');
    Route::post('/post/edit/{post}', 'update')->name('post.update');
    Route::get('/post/ip/{post}', 'ip')->name('post.ip');
    Route::get('/post/delete/{post}', 'delete')->name('post.delete');
    Route::post('/post/delete/{post}', 'destroy')->name('post.destroy');
});

Route::post('/transfer/transfer/{thread}', [TransferController::class, 'transfer'])->name('transfer.transfer');

Route::get('/img/character_avatar.id/thumb/{path}.jpg', function (string $path) {
    return redirect(Storage::disk('public')->url('character-avatars/thumb/' . $path . '.jpg'), 301);
})->where('path', '.*')->name('character_avatar.legacy_thumb');

Route::get('/img/character_avatar.id/{path}.jpg', function (string $path) {
    return redirect(Storage::disk('public')->url('character-avatars/' . $path . '.jpg'), 301);
})->where('path', '.*')->name('character_avatar.legacy_full');

Route::get('/img/emoticon/{file}', function (string $file) {
    return redirect(asset('images/emoticon/' . $file), 301);
})->where('file', '[0-9]+\.gif');

Route::get('/img/territory.id/{file}', function (string $file) {
    return redirect(asset('images/territory/' . $file), 301);
})->where('file', '[0-9]+\.png')->name('territory.legacy_coat_of_arms');

Route::get('/img/company_worker.type/{file}', function (string $file) {
    return redirect(asset('images/company-worker/' . $file), 301);
})->where('file', '[0-9]+\.png')->name('company.legacy_worker_type');

Route::get('/img/item.img/{file}', function (string $file) {
    return redirect(asset('images/item/' . $file), 301);
})->where('file', '[0-9]+\.png')->name('item.legacy_image');

Route::controller(ConversationController::class)->group(function () {
    Route::get('/conversation', 'index')->name('conversation');
    Route::get('/conversation/view/{user}', 'view')->name('conversation.view');
    Route::post('/conversation/create/{user}', 'create')->name('conversation.create');
});

Route::controller(UserController::class)->group(function () {
    Route::get('/user', 'index')->name('user');
    Route::get('/user/viewall/{order?}/{page?}', 'viewAll')->name('user.viewall');
    Route::get('/user/view/{user}', 'view')->name('user.view');
    Route::match(['get', 'post'], '/user/edit/{user}', 'edit')->name('user.edit');
    Route::match(['get', 'post'], '/user/createcharacter/{user}', 'createCharacter')->name('user.create_character');
    Route::get('/user/character/{character}', 'character')->name('user.character');
    Route::match(['get', 'post'], '/user/editcharacter/{character}', 'editCharacter')->name('user.edit_character');
    Route::match(['get', 'post'], '/user/createcontact/{user}', 'createContact')->name('user.create_contact');
    Route::match(['get', 'post'], '/user/editcontact/{contact}', 'editContact')->name('user.edit_contact');
    Route::match(['get', 'post'], '/user/deletecontact/{contact}', 'deleteContact')->name('user.delete_contact');
});

Route::controller(GroupController::class)->group(function () {
    Route::get('/group', 'index')->name('group');
    Route::get('/group/view/{group}/{page?}', 'view')->name('group.view');
});
Route::any('/group/{legacyPath}', fn () => abort(404))->where('legacyPath', '.*');

Route::controller(CompanyController::class)->group(function () {
    Route::get('/company', 'index')->name('company');
    Route::get('/company/viewall', 'viewAll')->name('company.viewall');
    Route::get('/company/view/{company}', 'view')->name('company.view');
    Route::get('/company/worker/{worker}', 'worker')->name('company.worker');
    Route::post('/company/worker/{worker}', 'assignLabour')->name('company.assign_labour');
    Route::get('/company/hire/{company}/{type?}', 'hire')->name('company.hire');
    Route::get('/company/fire/{worker}', 'fire')->name('company.fire');
    Route::get('/company/pay/{company}', 'pay')->name('company.pay');
});

Route::controller(EncyclopediaController::class)->group(function () {
    Route::get('/encyclopedia', 'index')->name('encyclopedia');
    Route::get('/encyclopedia/view/{page}', 'view')->name('encyclopedia.view');
    Route::match(['get', 'post'], '/encyclopedia/create/{page}', 'create')->name('encyclopedia.create');
    Route::match(['get', 'post'], '/encyclopedia/edit/{page}', 'edit')->name('encyclopedia.edit');
    Route::match(['get', 'post'], '/encyclopedia/delete/{page}', 'delete')->name('encyclopedia.delete');
});

Route::controller(DictionaryController::class)->group(function () {
    Route::get('/dictionary', 'index')->name('dictionary');
    Route::get('/dictionary/viewall/{languageFrom?}/{languageTo?}/{order?}', 'viewAll')->name('dictionary.viewall');
    Route::get('/dictionary/view/{word}', 'view')->name('dictionary.view');
    Route::match(['get', 'post'], '/dictionary/create', 'create')->name('dictionary.create');
    Route::match(['get', 'post'], '/dictionary/edit/{word}', 'edit')->name('dictionary.edit');
    Route::match(['get', 'post'], '/dictionary/delete/{word}', 'delete')->name('dictionary.delete');
    Route::match(['get', 'post'], '/dictionary/createkey/{word}', 'createKey')->name('dictionary.create_key');
    Route::match(['get', 'post'], '/dictionary/deletekey/{key}', 'deleteKey')->name('dictionary.delete_key');
    Route::match(['get', 'post'], '/dictionary/ajax__getwords', 'ajaxGetWords')
        ->name('dictionary.ajax_get_words')
        ->withoutMiddleware([VerifyCsrfToken::class]);
});

Route::controller(TerritoryController::class)->group(function () {
    Route::get('/territory', 'index')->name('territory');
    Route::get('/territory/view/{territory}', 'view')->name('territory.view');
    Route::get('/territory/land.geojson', 'landGeoJson')->name('territory.land_geojson');
    Route::get('/territory/{territory}/children.geojson', 'childrenGeoJson')->name('territory.children_geojson');
    Route::get('/territory/{territory}/settlements.geojson', 'settlementsGeoJson')->name('territory.settlements_geojson');
});
Route::any('/territory/{legacyPath}', fn () => abort(404))->where('legacyPath', '.*');

Route::controller(LogController::class)->group(function () {
    Route::post('/log/in', 'in')->name('log.in');
    Route::get('/log/out', 'out')->name('log.out');
    Route::match(['get', 'post'], '/log/forgot-password', 'forgotPassword')->name('log.forgot_password');
    Route::match(['get', 'post'], '/log/new-password/{email}/{key}', 'newPassword')->name('log.new_password');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'start')->name('register');
    Route::post('/register/validation', 'validation')->name('register.validation');
    Route::match(['get', 'post'], '/register/registration/{email}/{key}', 'registration')->name('register.registration');
    Route::get('/register/welcome', 'welcome')->name('register.welcome');
});

Route::controller(StaticPageController::class)->group(function () {
    Route::get('/static/help', 'help')->name('static.help');
    Route::get('/static/terms', 'terms')->name('static.terms');
    Route::get('/static/legal', 'legal')->name('static.legal');
});

// LEGACY
Route::any('{path}', LegacyController::class)->where('path', '.*')->withoutMiddleware(['VerifyCsrfToken']);
