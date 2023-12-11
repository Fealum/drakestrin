<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CalendarController extends Controller
{
    public function view()
    {
        return view('calendar.view');
    }
}
