<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Handles LEGACY requests
class LegacyController extends Controller
{
    public function __invoke(Request $request)
    {
        $url = $request->get('url', 'index');

        ob_start();
        require app_path('Http') . '/legacy.php';
        $output = ob_get_clean();

        return new Response($output);
    }
}
