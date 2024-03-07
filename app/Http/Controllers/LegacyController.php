<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Models\Online;

// Handles LEGACY requests
class LegacyController extends Controller
{
    public function __invoke(Request $request)
    {
        $path = $request->path() === '/' ? 'index' : $request->path();
        $urlArray = explode("/", $path);
        $object = $urlArray[0];
        array_shift($urlArray);
        $action = (isset($urlArray[0]) && $urlArray[0] != '') ? $urlArray[0] : 'std';
        array_shift($urlArray);
        $queryString = $urlArray;

        return $this->render($object, $action, $queryString);
    }

    public function render($object, $action, $queryString = [])
    {
        if (Auth::check() && $this->setonline) {
            $this->online = Online::where('user', Auth::id())
                ->update([
                    'controller' => $object,
                    'action' => $action
                ]);
        }

        View::share('online', Online::all());

        ob_start();
        require app_path('Http') . '/legacy.php';
        $output = ob_get_clean();

        if (str_starts_with($output, '##REDIRECT##')) {
            return redirect(substr($output, 12));
        }

        return new Response($output);
    }
}
