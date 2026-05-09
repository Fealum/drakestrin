<x-main-layout :title="'Wort: »'.$word->word.'«'" css="dictionary">
    <p>
        {{ $word->wordType?->name }}.
        @if ($canEdit)
        <a href="{{ route('dictionary.edit', ['word' => $word->id]) }}" class="option edit" title="Wort bearbeiten">Wort bearbeiten</a>
        @endif
        @if ($canDelete)
        <a href="{{ route('dictionary.delete', ['word' => $word->id]) }}" class="option delete" title="Wort löschen">Wort löschen</a>
        @endif
    </p>

    @if ($word->wordType?->code === 'sw')
    <h3>Derivation</h3>
    <p>Substantivierung: tay:{{ $word->word }}, tay:{{ $word->word }}&middot;{{ in_array(substr($word->word, -1), ['a', 'e', 'o', 'u'], true) ? 'd' : '' }}ae</p>
    <p>Adjektivierung: {{ $word->word }}&middot;is</p>
    <p>Verbisierung: la {{ $word->word }}&middot;la</p>
    @endif

    @if ($word->translationKeysFrom->isNotEmpty() || $canCreate)
    <h3>Übersetzungen</h3>
    <ol>
        @foreach ($word->translationKeysFrom as $key)
        <li>
            <a href="{{ route('dictionary.view', ['word' => $key->toWord->id]) }}">{{ $key->toWord->word }}</a>,
            <em>{{ $key->toWord->wordType?->name }}</em>.
            @if ($canDelete)
            <a href="{{ route('dictionary.delete_key', ['key' => $key->id]) }}" class="option delete" title="Verknüpfung löschen">Verknüpfung löschen</a>
            @endif
        </li>
        @endforeach
        @if ($canCreate)
        <li><a href="{{ route('dictionary.create_key', ['word' => $word->id]) }}" class="option create" title="Neue Verknüpfung erstellen">Neue Verknüpfung erstellen</a></li>
        @endif
    </ol>
    @endif
</x-main-layout>
