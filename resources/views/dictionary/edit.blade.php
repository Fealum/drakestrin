<x-main-layout :title="'Wort »'.$word->word.'« bearbeiten'" css="dictionary">
    <form name="editdictionary" action="{{ route('dictionary.edit', ['word' => $word->id]) }}" method="post">
        @csrf
        <p>
            <label for="language">Sprache: </label>
            <select name="language" id="language">
                @foreach ($languages as $language)
                <option value="{{ $language->id }}" @selected((old('language') ?? $word->language) == $language->id)>{{ $language->name }} ({{ $language->code }})</option>
                @endforeach
            </select>
        </p>
        <p>
            <label for="wordtype">Wortart: </label>
            <select name="wordtype" id="wordtype">
                @foreach ($wordTypes as $wordType)
                <option value="{{ $wordType->id }}" @selected((old('wordtype') ?? $word->wordtype) == $wordType->id)>{{ $wordType->name }} ({{ $wordType->code }})</option>
                @endforeach
            </select>
        </p>
        <x-textinput formname="editdictionary" inputname="word" displayname="Wort" :value="old('word') ?? $word->word" />
        <p><input type="submit" value="Wort bearbeiten" name="submit" /></p>
    </form>
</x-main-layout>
