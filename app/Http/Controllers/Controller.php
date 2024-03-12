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
use App\Services\PermissionService;
use Illuminate\Database\Eloquent\Relations\Relation;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $setonline = true;
    protected $online = null;

    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    protected function flashMessage(string $level, string $template, array $data = [])
    {
        $messages = session('flash_messages', []);
        $messages[] = [
            'level' => $level,
            'content' => view('notifications.' . $template, $data)->render(),
        ];

        session()->flash('flash_messages', $messages);
    }

    function setLocation($object)
    {
        if (Auth::check()) {
            $online = Online::where('user', Auth::id())->first();
            $online->locateable_type = array_search(get_class($object), Relation::morphMap(), true);
            $online->locateable_id = $object->id;
            $online->save();

            View::share('online', Online::all());
        }
    }
}
