<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IndexController extends Controller
{
    public function index()
    {
        $legacyController = new LegacyController($this->permissionService);
        return $legacyController->render('index', 'std');
    }
}
