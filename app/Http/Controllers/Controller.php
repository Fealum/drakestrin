<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Models\Online;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $setonline = true;
    protected $online = null;

    function __construct()
    {
        if (Auth::check() && $this->setonline) {
            $currentRoute = Route::current();
            $this->online = Online::updateOrCreate(
                ['user' => Auth::id()],
                [
                    'time' => Carbon::now(),
                    'ip' => Request::ip(),
                    'browser' => Request::userAgent(),
                    'controller' => NULL,
                    'action' => NULL,
                    'table__location' => NULL,
                    'location' => NULL,
                    'route' => Route::current()->getName(),
                ]
            );
            $this->online->locateable()->detach();
        }

        View::share('online', Online::all());
    }
}
