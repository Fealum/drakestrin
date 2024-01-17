<?php

namespace App\Http\Middleware;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class CheckNewConversation
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return $next($request);
        }

        $conversation = Conversation::where('user__recipient', Auth::id())
            ->where('view', 0)
            ->exists();

        View::share('newConv', $conversation);

        return $next($request);
    }
}
