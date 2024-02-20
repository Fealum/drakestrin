<?php

namespace App\Http\Middleware;

use App\Models\Message;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class CheckNewMessage
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return $next($request);
        }

        $message = Message::where('recipient_user_id', Auth::id())
            ->where('view', 0)
            ->exists();

        View::share('newMessage', $message);

        return $next($request);
    }
}
