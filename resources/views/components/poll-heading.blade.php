@props(['question', 'visibility', 'maxChoices' => 1])
<h4>{{ $question }}</h4>
<p><small>
    {{ ($visibility instanceof \App\Support\PollVisibility ? $visibility->value : $visibility) === 'open' ? 'Offene Abstimmung' : 'Anonyme Abstimmung' }}
    @if($maxChoices > 1)
        , bis zu {{ $maxChoices }} Antworten
    @endif
</small></p>
