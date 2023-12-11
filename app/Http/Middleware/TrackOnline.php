<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use App\Models\Online;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class TrackOnline
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return $next($request);
        }

        $currentRoute = Route::current();
        $controllerAction = explode('@', $currentRoute->getActionName());
        $controller = Str::replaceLast('Controller', '', class_basename($controllerAction[0]));
        $action = $controllerAction[1] ?? null;
        $online = Online::updateOrCreate(
            ['user' => $request->user()->id],
            [
                'time' => now(),
                'ip' => $request->ip(),
                'browser' => $request->userAgent(),
                'controller' => $controller,
                'action' => $action,
                'table__location' => NULL,
                'location' => NULL,
                'route' => Route::current()->getName(),
            ]
        );
        $online->locateable()->detach();

        foreach (Online::where('time', '<', now()->subMinutes(config('auth.user_timeout'))->getTimestamp())->get() as $oldOnline) {
            $oldOnline->user_legacy->lastvisit = $oldOnline->time;
            $oldOnline->user_legacy->save();
            $oldOnline->delete();
        };

        View::share('online', Online::all());

        return $next($request);
    }
}
