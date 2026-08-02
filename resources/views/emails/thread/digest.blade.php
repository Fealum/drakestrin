<p>Hallo {{ $recipient->name }},</p>

<p>In Deinen abonnierten Themen gibt es neue Beiträge:</p>

<ul>
    @foreach ($entries as $entry)
    <li>
        <a href="{{ route('post.view', $entry['first_post']) }}">{{ $entry['thread']->name }}</a>:
        {{ $entry['count'] }} {{ $entry['count'] === 1 ? 'neuer Beitrag' : 'neue Beiträge' }},
        zuletzt von {{ $entry['last_post']->character?->name ?? $entry['last_post']->author?->name ?? 'Unbekannt' }}
        (<a href="{{ URL::signedRoute('subscription.unsubscribe.confirm', ['subscription' => $entry['subscription']->id]) }}">nicht mehr abonnieren</a>)
    </li>
    @endforeach
</ul>

<p><a href="{{ route('subscriptions.index') }}">Abonnements verwalten</a></p>
