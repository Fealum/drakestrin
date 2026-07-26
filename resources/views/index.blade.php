<x-main-layout title="Sali Vuz!" css="index" :no-breadcrumbs="true">
    <p class="initial">Herzlich Willkommen in Drachenstein! Vielleicht fragt Ihr Euch nun, wo Ihr hier gelandet seid, doch das ist schnell erklärt: Drachenstein ist eine sogenannte Mikronation, ein virtueller Staat im Internet, und gleichzeitig ein Browserrollenspiel. Wir simulieren ein Kaiserreich in einer Fantasiewelt, seine Politik, seine Wirtschaft, und natürlich auch sein alltägliches Leben. Möchtet Ihr mitmachen? Nichts leichter als das! Registriert Euch, denkt Euch einen Charakter aus, und stellt im Forum einen Antrag auf Staatsbürgerschaft. Wir freuen uns auf Euch!</p>
    <div id="index-tiles">
        <div id="index-stats">
            <a href="{{ url('/') }}/board/filter/date_last:1d"><em>{{ $postCount24h }}</em> Beiträge in den letzten 24 Stunden;</a>
            <a href="{{ url('/') }}/board/filter/date_last:7d"><em>{{ $postCount7d }}</em> Beiträge in den letzten sieben Tagen.</a>
        </div>
        @if ($news)
        <div id="index-news">
            <img src="{{ asset('css/img/eksnor.png') }}" />
            <p><x-datetime :time="$news->time" /></p>
            <p>{{ $news->message }}</p>
        </div>
        @endif
        @if ($word)
        <div id="index-word">
            <a href="{{ route('dictionary.view', ['word' => $word->id]) }}">{{ $word->word }}</a>
            @forelse ($word->translationKeysFrom as $key)
            <p>{{ $key->toWord->word }},&nbsp;<em class="small">{{ $key->toWord->wordType?->code }}.</em></p>
            @empty
            &ndash;&ndash;
            @endforelse
        </div>
        @endif
    </div>
</x-main-layout>
