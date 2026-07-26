<x-main-layout title="Neues Wort erstellen">
    <form name="createdictionary" action="{{ route('dictionary.create') }}" method="post">
        @csrf
        <p>
            <label for="language_id">Sprache: </label>
            <select name="language_id" id="language_id">
                @foreach ($languages as $language)
                <option value="{{ $language->id }}" @selected(old('language_id') == $language->id)>{{ $language->name }} ({{ $language->code }})</option>
                @endforeach
            </select>
        </p>
        <p>
            <label for="word_type_id">Wortart: </label>
            <select name="word_type_id" id="word_type_id">
                @foreach ($wordTypes as $wordType)
                <option value="{{ $wordType->id }}" @selected(old('word_type_id') == $wordType->id)>{{ $wordType->name }} ({{ $wordType->code }})</option>
                @endforeach
            </select>
        </p>
        <x-textinput formname="createdictionary" inputname="word" displayname="Wort" :value="old('word')" />

        <h3>Erweiterter Modus:</h3>
        <p>
            <label for="advanced-language-from">Von: </label>
            <select name="advanced-language-from" id="advanced-language-from">
                @foreach ($languages as $language)
                <option value="{{ $language->id }}" @selected(old('advanced-language-from') == $language->id)>{{ $language->name }} ({{ $language->code }})</option>
                @endforeach
            </select><br />
            <label for="advanced-language-to">Nach: </label>
            <select name="advanced-language-to" id="advanced-language-to">
                @foreach ($languages as $language)
                <option value="{{ $language->id }}" @selected(old('advanced-language-to') == $language->id)>{{ $language->name }} ({{ $language->code }})</option>
                @endforeach
            </select><br />
            <textarea name="advanced">{{ old('advanced') }}</textarea>
        </p>
        <p><input type="submit" value="Neues Wort erstellen" name="submit" /></p>
    </form>
</x-main-layout>
