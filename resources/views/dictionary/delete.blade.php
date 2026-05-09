<x-main-layout :title="'Wort »'.$word->word.'« löschen'">
    <h3>Sicher?</h3>
    <p>Bist Du Dir sicher, dass Du dieses Wort löschen möchtest?</p>
    <p>Bei einer Löschung werden folgende Verknüpfungen ebenfalls gelöscht:</p>
    <ul>
        @if ($word->translationKeysFrom->isNotEmpty())
        <li>Von &raquo;{{ $word->word }}&laquo; zu &hellip;
            <ul>
                @foreach ($word->translationKeysFrom as $key)
                <li><a href="{{ route('dictionary.view', ['word' => $key->toWord->id]) }}">{{ $key->toWord->word }}</a></li>
                @endforeach
            </ul>
        </li>
        @endif
        @if ($word->translationKeysTo->isNotEmpty())
        <li>Zu &raquo;{{ $word->word }}&laquo; von &hellip;
            <ul>
                @foreach ($word->translationKeysTo as $key)
                <li><a href="{{ route('dictionary.view', ['word' => $key->fromWord->id]) }}">{{ $key->fromWord->word }}</a></li>
                @endforeach
            </ul>
        </li>
        @endif
    </ul>
    <form name="deletedictionary" action="{{ route('dictionary.delete', ['word' => $word->id]) }}" method="post">
        @csrf
        <input type="hidden" name="delete" value="1" />
        <p><input type="submit" value="Wort löschen" name="submit" /></p>
    </form>
</x-main-layout>
