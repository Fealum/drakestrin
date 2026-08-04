@php($type = $element['type'] ?? 'message')
@php($validTargets = collect($moveTargets[$index] ?? [$index]))
@php($previousTarget = $validTargets->filter(fn($target) => $target < $index)->last())
@php($nextTarget = $validTargets->first(fn($target) => $target > $index))
<section class="composer-element composer-element-{{ $type }}" data-index="{{ $index }}">
    <input type="hidden" name="elements[{{ $index }}][type]" value="{{ $type }}">
    <header>
        <strong>{{ match($type) { 'message' => 'Text', 'transfer' => 'Handlung', 'scene_transition' => 'Szenenwechsel', 'poll' => 'Umfrage', default => 'Baustein' } }}</strong>
        @if($previousTarget !== null)<button type="submit" name="intent" value="move:{{ $index }}:{{ $previousTarget }}" title="Nach oben verschieben"><span class="fa fa-arrow-up" aria-hidden="true"></span></button>@endif
        @if($nextTarget !== null)<button type="submit" name="intent" value="move:{{ $index }}:{{ $nextTarget }}" title="Nach unten verschieben"><span class="fa fa-arrow-down" aria-hidden="true"></span></button>@endif
        <button type="submit" name="intent" value="remove:{{ $index }}">Entfernen</button>
    </header>

    @if($type === 'message')
        <x-bbcode-textarea :name="'elements['.$index.'][message]'" :id="'element-message-'.$index" :value="$element['message'] ?? ''" />
        <label><input type="checkbox" name="elements[{{ $index }}][smilies]" value="1" @checked($element['smilies'] ?? true)> Smileys anzeigen</label>
    @elseif($type === 'scene_transition')
        <input type="hidden" name="elements[{{ $index }}][scene_key]" value="{{ $element['scene_key'] ?? '' }}">
        <label>Wechsel <select name="elements[{{ $index }}][scene_action]"><option value="start" @selected(($element['scene_action'] ?? 'start') === 'start')>Szene beginnen oder wechseln</option><option value="end" @selected(($element['scene_action'] ?? '') === 'end')>Szene beenden</option></select></label>
        <label>Ort <select name="elements[{{ $index }}][location_id]"><option value="">Kein Ort</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((int) ($element['location_id'] ?? 0) === $location->id)>{{ $location->name }}</option>@endforeach</select></label>
        <label>Spielzeit <input type="datetime-local" name="elements[{{ $index }}][story_at]" value="{{ filled($element['story_at'] ?? null) ? \Illuminate\Support\Carbon::createFromTimestamp((int) $element['story_at'])->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}"></label>
    @elseif($type === 'poll')
        <label>Frage <input name="elements[{{ $index }}][question]" maxlength="255" value="{{ $element['question'] ?? '' }}"></label>
        @foreach(($element['options'] ?? ['', '']) as $optionIndex => $option)
            <label>Antwort {{ $optionIndex + 1 }} <input name="elements[{{ $index }}][options][{{ $optionIndex }}]" maxlength="255" value="{{ $option }}"></label>
            @if(count($element['options'] ?? []) > 2)<button type="submit" name="intent" value="remove_poll_option:{{ $index }}:{{ $optionIndex }}">Antwort entfernen</button>@endif
        @endforeach
        <button type="submit" name="intent" value="add_poll_option:{{ $index }}">Antwort hinzufügen</button>
        <label>Abstimmung <select name="elements[{{ $index }}][visibility]"><option value="anonymous" @selected(($element['visibility'] ?? '') === 'anonymous')>Anonym</option><option value="open" @selected(($element['visibility'] ?? '') === 'open')>Offen</option></select></label>
        <label>Höchstens <input type="number" min="1" max="{{ count($element['options'] ?? ['', '']) }}" name="elements[{{ $index }}][max_choices]" value="{{ $element['max_choices'] ?? 1 }}"> Antworten</label>
        <label>Abstimmungsschluss <input type="datetime-local" name="elements[{{ $index }}][closes_at]" value="{{ filled($element['closes_at'] ?? null) ? \Illuminate\Support\Carbon::createFromTimestamp((int) $element['closes_at'])->format('Y-m-d\TH:i') : '' }}"></label>
    @elseif($type === 'transfer')
        @php($transferContext = $transferContexts[$index] ?? ['locationInventory' => collect(), 'sites' => collect()])
        <input type="hidden" name="elements[{{ $index }}][scene_key]" value="{{ $element['scene_key'] ?? '' }}">
        <label>Handlung <select name="elements[{{ $index }}][transfer_action]">
            <option value="give" @selected(($element['transfer_action'] ?? '') === 'give')>An Charakter geben</option>
            <option value="drop" @selected(($element['transfer_action'] ?? '') === 'drop')>Am Ort ablegen</option>
            <option value="pickup" @selected(($element['transfer_action'] ?? '') === 'pickup')>Vom Ort aufnehmen</option>
            <option value="company_deposit" @selected(($element['transfer_action'] ?? '') === 'company_deposit')>An örtlichen Betrieb geben</option>
            <option value="company_withdrawal" @selected(($element['transfer_action'] ?? '') === 'company_withdrawal')>Für örtlichen Betrieb aushändigen</option>
        </select></label>
        <x-character-selector :name="'elements['.$index.'][recipient]'" :input-id="'element-recipient-'.$index" :endpoint="route('board.ajax_get_chars')" label="Empfänger" placeholder="Empfänger suchen ..." :value="$element['recipient'] ?? null" />
        @if($transferContext['sites']->isNotEmpty())
        <label class="composer-company-site">Betrieb <select name="elements[{{ $index }}][company_site]">@foreach($transferContext['sites'] as $site)<option value="{{ $site->id }}" @selected((int)($element['company_site'] ?? 0) === $site->id)>{{ $site->company?->name }}@if($site->company?->sites->count() > 1) ({{ $site->name }})@endif</option>@endforeach</select></label>
        @endif
        @foreach($characters as $character)@foreach($character->inventory as $inventory)
        <label class="composer-inventory" data-source="character" data-owner="{{ $character->id }}"><input type="checkbox" name="elements[{{ $index }}][inventory][{{ $inventory->id }}]" value="{{ $inventory->id }}" @checked(isset($element['inventory'][$inventory->id]))> {{ $character->name }}: {{ $inventory->item?->name }} <input name="elements[{{ $index }}][inventorystack][{{ $inventory->id }}]" value="{{ $element['inventorystack'][$inventory->id] ?? $inventory->makeunitary() }}"></label>
        @endforeach @endforeach
        @foreach($transferContext['locationInventory'] as $inventory)
        <label class="composer-inventory" data-source="location"><input type="checkbox" name="elements[{{ $index }}][inventory][{{ $inventory->id }}]" value="{{ $inventory->id }}" @checked(isset($element['inventory'][$inventory->id]))> Am Ort: {{ $inventory->item?->name }} <input name="elements[{{ $index }}][inventorystack][{{ $inventory->id }}]" value="{{ $element['inventorystack'][$inventory->id] ?? $inventory->makeunitary() }}"></label>
        @endforeach
        @foreach($transferContext['sites'] as $site)@foreach($site->inventory as $inventory)
        <label class="composer-inventory" data-source="company" data-owner="{{ $site->id }}"><input type="checkbox" name="elements[{{ $index }}][inventory][{{ $inventory->id }}]" value="{{ $inventory->id }}" @checked(isset($element['inventory'][$inventory->id]))> {{ $site->company?->name }}: {{ $inventory->item?->name }} <input name="elements[{{ $index }}][inventorystack][{{ $inventory->id }}]" value="{{ $element['inventorystack'][$inventory->id] ?? $inventory->makeunitary() }}"></label>
        @endforeach @endforeach
    @endif
</section>
