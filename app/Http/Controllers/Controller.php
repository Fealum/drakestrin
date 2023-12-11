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

    protected function flashMessage(string $level, string $template, array $data = [])
    {
        $messages = session('flash_messages', []);
        $messages[] = [
            'level' => $level,
            'content' => view('notifications.' . $template, $data)->render(),
        ];

        session()->flash('flash_messages', $messages);
    }
}
