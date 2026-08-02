<?php

namespace App\Http\Controllers;

use App\Models\Board\Thread;
use App\Models\Board\ThreadSubscription;
use App\Services\Board\ThreadReadService;
use App\Services\Board\ThreadSubscriptionService;
use App\Services\PermissionService;
use App\Support\ThreadEmailFrequency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ThreadSubscriptionController extends Controller
{
    public function __construct(
        PermissionService $permissionService,
        private ThreadSubscriptionService $subscriptions,
        private ThreadReadService $reads,
    ) {
        parent::__construct($permissionService);
    }

    public function index(Request $request): View
    {
        abort_unless($request->user(), 403);
        $subscriptions = ThreadSubscription::query()
            ->with(['thread.board', 'thread.lastPost.character'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('updated_at')
            ->get()
            ->filter(fn ($subscription) => $subscription->thread && Gate::forUser($request->user())->allows('view', $subscription->thread))
            ->values();
        $unreadIds = $this->reads->unreadThreadIds($subscriptions->pluck('thread'), $request->user());
        $firstUnreadPosts = $subscriptions->mapWithKeys(fn ($subscription) => [
            $subscription->thread_id => $unreadIds->contains($subscription->thread_id)
                ? $this->reads->firstUnreadPost($subscription->thread, $request->user())?->id
                : null,
        ]);

        return view('subscription.index', [
            'firstUnreadPosts' => $firstUnreadPosts,
            'frequencies' => ThreadEmailFrequency::cases(),
            'subscriptions' => $subscriptions,
            'unreadIds' => $unreadIds,
        ]);
    }

    public function store(Request $request, Thread $thread): RedirectResponse
    {
        abort_unless($request->user(), 403);
        $this->authorize('view', $thread);
        $this->subscriptions->subscribe($request->user(), $thread);

        return back()->with('status', 'Das Thema wird nun abonniert.');
    }

    public function destroy(Request $request, Thread $thread): RedirectResponse
    {
        abort_unless($request->user(), 403);
        ThreadSubscription::query()
            ->where('user_id', $request->user()->id)
            ->where('thread_id', $thread->id)
            ->delete();

        return back()->with('status', 'Das Abonnement wurde beendet.');
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user(), 403);
        $data = $request->validate([
            'subscriptions' => ['nullable', 'array'],
            'subscriptions.*' => ['required', Rule::enum(ThreadEmailFrequency::class)],
            'remove' => ['nullable', 'array'],
            'remove.*' => ['integer'],
        ]);
        $owned = ThreadSubscription::query()->where('user_id', $request->user()->id);
        $remove = collect($data['remove'] ?? [])->map(fn ($id) => (int) $id);

        if ($remove->isNotEmpty()) {
            (clone $owned)->whereIn('id', $remove)->delete();
        }

        foreach ($data['subscriptions'] ?? [] as $id => $frequency) {
            $subscription = (clone $owned)->with('thread')->find((int) $id);
            if (! $subscription || $remove->contains($subscription->id)) {
                continue;
            }

            if ($subscription->email_frequency->value !== $frequency) {
                $subscription->update([
                    'email_frequency' => $frequency,
                    'last_emailed_post_id' => $subscription->thread?->last_post_id ?: null,
                ]);
            }
        }

        return back()->with('status', 'Die Abonnements wurden gespeichert.');
    }

    public function settings(Request $request): View|RedirectResponse
    {
        abort_unless($request->user(), 403);
        $preference = $this->reads->preference($request->user());

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'auto_subscribe' => ['nullable', 'boolean'],
                'default_email_frequency' => ['required', Rule::enum(ThreadEmailFrequency::class)],
            ]);
            $preference->update([
                'auto_subscribe' => (bool) ($data['auto_subscribe'] ?? false),
                'default_email_frequency' => $data['default_email_frequency'],
            ]);
            $request->user()->update([
                'receiveemails' => $data['default_email_frequency'] !== ThreadEmailFrequency::NONE->value,
            ]);

            return back()->with('status', 'Die Forum-Einstellungen wurden gespeichert.');
        }

        return view('subscription.settings', [
            'frequencies' => ThreadEmailFrequency::cases(),
            'preference' => $preference,
        ]);
    }

    public function subscribers(Request $request, Thread $thread): View
    {
        $this->authorize('view', $thread);
        abort_unless($request->user() && $this->permissionService->allows('viewthreadsubscriptions', $thread, $request->user()), 403);

        return view('subscription.subscribers', [
            'subscribers' => $thread->subscribers()->orderByRaw('LOWER(name)')->get(),
            'thread' => $thread,
        ]);
    }

    public function unsubscribeConfirmation(Request $request, ThreadSubscription $subscription): View
    {
        abort_unless($request->hasValidSignature(), 403);

        return view('subscription.unsubscribe', ['subscription' => $subscription->load('thread')]);
    }

    public function signedDestroy(Request $request, ThreadSubscription $subscription): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);
        $subscription->delete();

        return redirect()->route('board')->with('status', 'Das Abonnement wurde beendet.');
    }
}
