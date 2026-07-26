@if ($errors->any())
<div class="notice notice_error">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<p>
    <label for="name">Name</label><br>
    <input id="name" name="name" type="text" maxlength="255" required value="{{ old('name', $company->name ?? '') }}">
</p>

<p>
    <label for="owner_character_id">Eigentümer</label><br>
    @if ($company->exists)
    <input type="hidden" name="owner_character_id" value="{{ $company->character_id }}">
    <strong>{{ $company->character?->name }}</strong>
    @else
    <select id="owner_character_id" name="owner_character_id" required>
        @foreach ($characters as $character)
        <option value="{{ $character->id }}" @selected(old('owner_character_id') == $character->id)>{{ $character->name }}</option>
        @endforeach
    </select>
    @endif
</p>

<p>
    <label for="sector">Wirtschaftszweig</label><br>
    <select id="sector" name="sector" required>
        @foreach ($sectors as $sector)
        <option value="{{ $sector->value }}" @selected((int) old('sector', $company->type ?? 1) === $sector->value)>{{ $sector->label() }}</option>
        @endforeach
    </select>
</p>

<p>
    <label for="location_id">Hauptsitz</label><br>
    <select id="location_id" name="location_id" required>
        @foreach ($locations as $location)
        <option value="{{ $location['id'] }}" @selected((int) old('location_id', $company->headquarters?->location_id) === (int) $location['id'])>{{ $location['label'] }}</option>
        @endforeach
    </select>
</p>

<p>
    <input type="hidden" name="is_storefront" value="0">
    <label>
        <input type="checkbox" name="is_storefront" value="1" @checked((bool) old('is_storefront', $company->headquarters?->is_storefront ?? true))>
        Am Hauptsitz Waren anbieten
    </label>
</p>

<p>
    <label for="description">Kurzbeschreibung</label><br>
    <textarea id="description" name="description">{{ old('description', $company->description ?? '') }}</textarea>
</p>

<p>
    <label for="text">Ausführliche Beschreibung</label><br>
    <textarea id="text" name="text">{{ old('text', $company->text ?? '') }}</textarea>
</p>

<p>
    <label for="url">Webseite</label><br>
    <input id="url" name="url" type="url" maxlength="2048" value="{{ old('url', $company->url ?? '') }}">
</p>

<button type="submit">{{ $submitLabel }}</button>
