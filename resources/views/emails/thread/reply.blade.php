<p>Hallo {{ $subscription->user->name }},</p>

<p>
    {{ $post->character?->name ?? $post->author?->name ?? 'Jemand' }} hat im Thema
    <a href="{{ route('post.view', $post) }}">{{ $post->thread->name }}</a>
    im Forum {{ $post->thread->board?->name }} geschrieben.
</p>

<blockquote>{{ \Illuminate\Support\Str::limit(strip_tags($post->contentSummary()), 300) }}</blockquote>

<p><a href="{{ route('post.view', $post) }}">Zum Beitrag</a></p>
<p>
    <a href="{{ route('subscriptions.index') }}">Abonnements verwalten</a> ·
    <a href="{{ URL::signedRoute('subscription.unsubscribe.confirm', ['subscription' => $subscription->id]) }}">Dieses Abonnement beenden</a>
</p>
