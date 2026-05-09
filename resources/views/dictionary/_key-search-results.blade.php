@if ($query !== '')
<h3>Suchergebnisse</h3>
<ol class="dictionary-key-results">
    @forelse ($searchResults as $result)
    <li>
        {{ $result->word }},
        <em>{{ $result->wordType?->name }}</em>,
        {{ $result->languageModel?->name ?? $result->languageModel?->code ?? $result->getAttribute('language') }}.
        <form class="dictionary-key-result-form" action="{{ route('dictionary.create_key', ['word' => $word->id]) }}" method="post">
            @csrf
            <input type="hidden" name="word" value="{{ $result->id }}" />
            <button type="submit" name="bijective" value="0">Einfach verknüpfen</button>
            <button type="submit" name="bijective" value="1">Bijektiv verknüpfen</button>
        </form>
    </li>
    @empty
    <li>Keine Wörter gefunden.</li>
    @endforelse
</ol>
@endif
