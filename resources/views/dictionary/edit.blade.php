<x-main-layout :title="'Wort »'.$word->word.'« bearbeiten'" css="dictionary">
    <form name="editdictionary" action="{{ route('dictionary.edit', ['word' => $word->id]) }}" method="post">
        @csrf
        <p>
            <label for="language_id">Sprache: </label>
            <select name="language_id" id="language_id">
                @foreach ($languages as $language)
                <option value="{{ $language->id }}" @selected((old('language_id') ?? $word->language_id) == $language->id)>{{ $language->name }} ({{ $language->code }})</option>
                @endforeach
            </select>
        </p>
        <p>
            <label for="word_type_id">Wortart: </label>
            <select name="word_type_id" id="word_type_id">
                @foreach ($wordTypes as $wordType)
                <option value="{{ $wordType->id }}" @selected((old('word_type_id') ?? $word->word_type_id) == $wordType->id)>{{ $wordType->name }} ({{ $wordType->code }})</option>
                @endforeach
            </select>
        </p>
        <x-textinput formname="editdictionary" inputname="word" displayname="Wort" :value="old('word') ?? $word->word" />
        <p><input type="submit" value="Wort bearbeiten" name="submit" /></p>
    </form>
</x-main-layout>
