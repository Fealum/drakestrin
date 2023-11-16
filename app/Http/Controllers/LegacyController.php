<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Online;

// Handles LEGACY requests
class LegacyController extends Controller
{
    public function __invoke(Request $request)
    {
        $urlArray = explode("/", $request->get('url', 'index') ?? 'index');

        $object = $urlArray[0];
        array_shift($urlArray);
        $action = (isset($urlArray[0]) && $urlArray[0] != '') ? $urlArray[0] : 'std';
        array_shift($urlArray);
        $queryString = $urlArray;

        if (Auth::check() && $this->setonline) {
            $this->online = Online::updateOrCreate(
                ['user' => Auth::id()],
                [
                    'controller' => $object,
                    'action' => $action,
                ]
            );
        }

        ob_start();
        require app_path('Http') . '/legacy.php';
        $output = ob_get_clean();

        return new Response($output);
    }
}
