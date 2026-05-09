<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\EncyclopediaController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\LegacyController;
use App\Http\Middleware\VerifyCsrfToken;

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

Route::controller(ConversationController::class)->group(function () {
    Route::get('/conversation', 'index')->name('conversation');
    Route::get('/conversation/view/{user}', 'view')->name('conversation.view');
    Route::post('/conversation/create/{user}', 'create')->name('conversation.create');
});

Route::controller(EncyclopediaController::class)->group(function () {
    Route::get('/encyclopedia', 'index')->name('encyclopedia');
    Route::get('/encyclopedia/view/{page}', 'view')->name('encyclopedia.view');
    Route::match(['get', 'post'], '/encyclopedia/create/{page}', 'create')->name('encyclopedia.create');
    Route::match(['get', 'post'], '/encyclopedia/edit/{page}', 'edit')->name('encyclopedia.edit');
    Route::match(['get', 'post'], '/encyclopedia/delete/{page}', 'delete')->name('encyclopedia.delete');
});

Route::controller(DictionaryController::class)->group(function () {
    Route::get('/dictionary', 'index')->name('dictionary.index');
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
