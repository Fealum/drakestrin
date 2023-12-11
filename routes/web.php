<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\LegacyController;

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

Route::get('/calendar', [CalendarController::class, 'view'])->name('calendar');

Route::post('/log/in', [LogController::class, 'in'])->name('log.in');
Route::get('/log/out', [LogController::class, 'out'])->name('log.out');
Route::get('/log/forgot-password', [LogController::class, 'forgotPassword'])->name('log.forgot-password');
Route::get('/log/new-password', [LogController::class, 'newPassword'])->name('log.new-password');

Route::get('/register', [RegisterController::class, 'start'])->name('register');
Route::post('/register/validation', [RegisterController::class, 'validation'])->name('register.validation');
Route::match(['get', 'post'], '/register/registration/{email}/{key}', [RegisterController::class, 'registration'])->name('register.registration');
Route::get('/register/welcome', [RegisterController::class, 'welcome'])->name('register.welcome');

Route::get('/static/help', [StaticPageController::class, 'help'])->name('static.help');
Route::get('/static/terms', [StaticPageController::class, 'terms'])->name('static.terms');
Route::get('/static/legal', [StaticPageController::class, 'legal'])->name('static.legal');

// LEGACY
Route::any('{path}', LegacyController::class)->where('path', '.*')->withoutMiddleware(['VerifyCsrfToken']);
