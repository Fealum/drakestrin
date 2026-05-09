<x-main-layout title="Diktionar" css="dictionary" :no-breadcrumbs="true">
    <p>Angezeigt werden {{ $words->count() }} Wörter.</p>
    <p class="columns">
        @foreach ($wordTypes as $wordType)
        <em class="small">{{ $wordType->code }}.</em> = {{ $wordType->name }}.<br />
        @endforeach
    </p>

    <h3>
        {{ $languageFrom->name }}&nbsp;&ndash;&nbsp;{{ $languageTo->name }}
        @if ($canCreate)
        <a href="{{ route('dictionary.create') }}" class="option create" title="Neues Wort erstellen">Neues Wort erstellen</a>
        @endif
    </h3>
    <ul class="columns">
        @forelse ($words as $word)
        <li>
            <strong><a href="{{ route('dictionary.view', ['word' => $word->id]) }}">{{ $word->word }}</a>,</strong>&nbsp;<em class="small">{{ $word->wordType?->code }}.</em>
            @forelse ($word->translationKeysFrom as $key)
            <a href="{{ route('dictionary.view', ['word' => $key->toWord->id]) }}">{{ $key->toWord->word }}</a>,&nbsp;<em class="small">{{ $key->toWord->wordType?->code }}.</em>
            @empty
            &ndash;&ndash;
            @endforelse
        </li>
        @empty
        <li>Keine Wörter gefunden!</li>
        @endforelse
    </ul>

    <h3><span>Historie</span></h3>
    <p>
        10. Juli 2006 - Erste funktionsfähige Version<br />
        Gleicher Tag, Spätabends - Darstellung in TMTe eingebaut<br />
        11. Juli 2006 - Nun gibt es eine einzelne Seite mit weiteren Informationen für jedes Wort und für Verben eine Konjugationstabelle.<br />
        Gleicher Tag, 17 Uhr 11 - Der zwanzigste Eintrag wurde hinzugefügt!<br />
        Gleicher Tag, 23 Uhr 34 - Der fünfzigste Eintrag wurde hinzugefügt!<br />
        17. Dezember 2006 - Möglichkeit für den User, selbst ein Wort einzutragen<br />
        Gleicher Tag, 14 Uhr 06 - Freischaltmöglichkeit dieser Worte für den Admin durch die Seite<br />
        Gleicher Tag, 19 Uhr 30 - Möglichkeit für den User, mehrere Worte gleichzeitig einzutragen<br />
        Gleicher Tag, 19 Uhr 53 - Der fünfundsiebzigste Eintrag wurde hinzugefügt!<br />
        Gleicher Tag, 23 Uhr 42 - Der hundertste Eintrag wurde hinzugefügt!<br />
        11. Juni 2007 - Der zweihundertste Eintrag wurde hinzugefügt!<br />
        12. Juni 2007 - Generelle Umänderung der Darstellung in Wörterbuchform<br />
        27. August 2011 - Generalüberarbeitung
    </p>
    <h3><span>Geplant</span></h3>
    <p>- Anzeige nur bestimmter Buchstaben möglich</p>
</x-main-layout>
