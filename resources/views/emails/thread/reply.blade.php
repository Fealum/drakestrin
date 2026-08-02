<p>Hallo {{ $subscription->user->name }},</p>

<p>
    {{ $post->character?->name ?? $post->author?->name ?? 'Jemand' }} hat im Thema
    <a href="{{ route('post.view', $post) }}">{{ $post->thread->name }}</a>
    im Forum {{ $post->thread->board?->name }} geschrieben.
</p>

@if (trim($post->message) !== '')
<blockquote>{{ \Illuminate\Support\Str::limit(strip_tags($post->message), 300) }}</blockquote>
@else
<p>Der Beitrag enthält eine Handlung.</p>
@endif

<p><a href="{{ route('post.view', $post) }}">Zum Beitrag</a></p>
<p>
    <a href="{{ route('subscriptions.index') }}">Abonnements verwalten</a> ·
    <a href="{{ URL::signedRoute('subscription.unsubscribe.confirm', ['subscription' => $subscription->id]) }}">Dieses Abonnement beenden</a>
</p>
