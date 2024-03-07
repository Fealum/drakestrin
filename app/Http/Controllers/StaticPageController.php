<?php

namespace App\Http\Controllers;

class StaticPageController extends Controller
{
    public function help()
    {
        return view('static.help');
    }

    public function terms()
    {
        return view('static.terms');
    }

    public function legal()
    {
        return view('static.legal');
    }
}
