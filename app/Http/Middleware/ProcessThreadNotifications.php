<?php

namespace App\Http\Middleware;

use App\Models\Board\ThreadSubscription;
use App\Services\Board\ThreadNotificationMailer;
use App\Services\Board\ThreadReadService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProcessThreadNotifications
{
    public function __construct(private ThreadNotificationMailer $mailer, private ThreadReadService $reads) {}

    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (Cache::add('forum:notification-request-processing', true, 60)) {
                $this->mailer->processPending();
            }
        } catch (Throwable $exception) {
            report($exception);
        }

        if ($request->user()) {
            $threads = ThreadSubscription::query()
                ->with('thread.board')
                ->where('user_id', $request->user()->id)
                ->get()
                ->pluck('thread')
                ->filter(fn ($thread) => $thread && Gate::forUser($request->user())->allows('view', $thread));
            View::share('newSubscribedThread', $this->reads->unreadThreadIds($threads, $request->user())->isNotEmpty());
        }

        return $next($request);
    }
}
