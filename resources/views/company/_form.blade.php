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
    <label for="name">Name</label>
    <input id="name" name="name" type="text" maxlength="255" required value="{{ old('name', $company->name ?? '') }}">
</p>

@unless ($company->exists)
<p>
    <label for="owner_character_id">Eigentümer</label>
    <select id="owner_character_id" name="owner_character_id" required>
        @foreach ($characters as $character)
        <option value="{{ $character->id }}" @selected(old('owner_character_id') == $character->id)>{{ $character->name }}</option>
        @endforeach
    </select>
</p>
@endunless

<p>
    <label for="sector">Wirtschaftszweig</label>
    @php($sectorLocked = $company->exists && ! ($canChangeSector ?? true))
    @if ($sectorLocked)
    <input type="hidden" name="sector" value="{{ $company->type }}">
    @endif
    <select id="sector"
        @if ($sectorLocked) disabled @else name="sector" required @endif
    >
        @foreach ($sectors as $sector)
        <option value="{{ $sector->value }}" @selected((int) ($sectorLocked ? $company->type : old('sector', $company->type ?? 1)) === $sector->value)>{{ $sector->label() }}</option>
        @endforeach
    </select>
    @if ($sectorLocked)
    <br><small>Der Wirtschaftszweig ist nach Einstellung von Beschäftigten oder Beginn der Produktion festgelegt.</small>
    @endif
</p>

@unless ($company->exists)
<fieldset class="company-location-choice">
    <legend>Standort</legend>
    <label>
        <input type="radio" name="location_mode" value="existing" @checked(old('location_mode', 'existing') === 'existing')>
        Bestehenden Ort wählen
    </label>
    <div class="company-location-option company-location-existing">
        <label for="location_id">Ort</label><br>
        <select id="location_id" name="location_id">
            @foreach ($locations as $location)
            <option value="{{ $location['id'] }}" @selected((int) old('location_id') === (int) $location['id'])>{{ $location['label'] }}</option>
            @endforeach
        </select>
    </div>
    @if ($fautheien->isNotEmpty())
    <label>
        <input type="radio" name="location_mode" value="new" @checked(old('location_mode') === 'new')>
        Neuen Ort für den Betrieb erstellen
    </label>
    <div class="company-location-option company-location-new">
        <label for="fauthei_id">Fauthei</label><br>
        <select id="fauthei_id" name="fauthei_id">
            @foreach ($fautheien as $fauthei)
            <option value="{{ $fauthei->id }}" @selected((int) old('fauthei_id') === (int) $fauthei->id)>{{ $fauthei->displayName() }}</option>
            @endforeach
        </select>
        <small>Der neue Ort erhält den Namen des Betriebs.</small>
    </div>
    @endif
</fieldset>
@endunless

<p>
    <label for="description">Beschreibung</label><br>
    <textarea id="description" name="description" maxlength="65535">{{ old('description', $company->description ?? '') }}</textarea>
</p>

<button type="submit">{{ $submitLabel }}</button>
