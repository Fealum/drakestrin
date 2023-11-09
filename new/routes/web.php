<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaticPageController;

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

use Legacy\Library\Class\Session;
use Legacy\Library\Class\Cache;
use Illuminate\Support\Facades\Auth;

$userSession = new Session();
if ($userSession->check() == FALSE) $userSession = new Session();
if ($userSession->userid && $userSession->userpw) {
    $thisUser = Cache::_('UserModel', $userSession->userid);
    if (!$thisUser->check_password($userSession->userpw)) $thisUser = FALSE;
} else {
    $thisUser = FALSE;
}

if (php_sapi_name() !== "cli") {
    if ($thisUser && Auth::guest()) {
        Auth::login(
            App\Models\User::findOrFail($userSession->userid)
        );
    } else if (!$thisUser) {
        Auth::logout();
    }
}

Route::get('/', function () {
    return view('welcome');
});

Route::get('/static/help', [StaticPageController::class, 'help'])->name('static.help');
Route::get('/static/terms', [StaticPageController::class, 'terms'])->name('static.terms');
Route::get('/static/legal', [StaticPageController::class, 'legal'])->name('static.legal');
