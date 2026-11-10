@php($type = $element['type'] ?? 'message')
@php($validTargets = collect($moveTargets[$index] ?? [$index]))
@php($previousTarget = $validTargets->filter(fn($target) => $target < $index)->last())
@php($nextTarget = $validTargets->first(fn($target) => $target > $index))
@php($sceneState = $elementSceneStates[$index] ?? ['active' => false])
@php($preview = $elementPreviews[$index] ?? null)
<section class="composer-element composer-element-{{ $type }} @if($sceneState['active']) is-in-scene @endif" data-index="{{ $index }}" data-editor-key="{{ $element['editor_key'] ?? '' }}" @if(in_array($element['editor_key'] ?? null, $insertedElementKeys ?? [], true)) data-inserted @endif data-scene-active="{{ $sceneState['active'] ? 1 : 0 }}" data-story-time="{{ $sceneState['story_at'] ?? '' }}" data-can-start="{{ $canSetScene ? 1 : 0 }}" data-can-end="{{ $canEndScene ? 1 : 0 }}" data-marker-options="{{ implode(',', $markerOptions[$index] ?? []) }}">
    <input type="hidden" name="elements[{{ $index }}][type]" value="{{ $type }}">
    @unless($type === 'message')
    @if(isset($element['editor_key']))<input type="hidden" name="elements[{{ $index }}][editor_key]" value="{{ $element['editor_key'] }}">@endif
    <header>
        <strong>{{ match($type) { 'message' => 'Text', 'transfer' => 'Übergabe', 'scene_transition' => 'Szene', 'poll' => 'Umfrage', default => 'Baustein' } }}</strong>
        <template x-if="true"><span class="composer-arrows">
        <button type="submit" formnovalidate name="intent" data-direction="up" value="{{ $previousTarget !== null ? 'move:'.$index.':'.$previousTarget : '' }}" @disabled($previousTarget === null) title="Nach oben verschieben" aria-label="Nach oben verschieben"><span class="option move-up" aria-hidden="true"></span></button>
        <button type="submit" formnovalidate name="intent" data-direction="down" value="{{ $nextTarget !== null ? 'move:'.$index.':'.$nextTarget : '' }}" @disabled($nextTarget === null) title="Nach unten verschieben" aria-label="Nach unten verschieben"><span class="option move-down" aria-hidden="true"></span></button>
        <button type="submit" formnovalidate name="intent" value="remove:{{ $index }}" class="composer-remove" @disabled(!($removableElements[$index] ?? false)) title="{{ ($removableElements[$index] ?? false) ? 'Baustein entfernen' : 'Dieser Baustein wird für den Szenenaufbau benötigt' }}" aria-label="Baustein entfernen"><span class="option delete" aria-hidden="true"></span></button>
        </span></template>
        <noscript>
            <label for="move-{{ $index }}">Position <select id="move-{{ $index }}" name="move_targets[{{ $index }}]">
                @foreach(range(0, $elementCount - 1) as $target)<option value="{{ $target }}" @selected((int) old('move_targets.'.$index, $index) === $target)>{{ $target + 1 }}</option>@endforeach
            </select></label>
            <label class="composer-remove-label"><input type="checkbox" name="remove_elements[{{ $index }}]" value="1" @checked(old('remove_elements.'.$index))> Entfernen</label>
        </noscript>
    </header>
    @endunless

    <div class="composer-element-content">
    @unless($type === 'message')
    <details class="composer-element-editor" @if(!$preview || $errors->any()) open @endif data-ready="{{ $preview ? 1 : 0 }}">
        <summary title="Bearbeiten"><span class="option edit" aria-hidden="true"></span><span class="sr-only">Bearbeiten</span></summary>
        <div class="composer-element-fields">
    @endunless
    @if($type === 'message')
        <x-bbcode-textarea :name="'elements['.$index.'][message]'" :id="'element-message-'.$index" :value="$element['message'] ?? ''" :autogrow="true" :toolbar="false" placeholder=" " label="Textabschnitt" />
        <input type="hidden" name="elements[{{ $index }}][smilies]" value="{{ ($element['smilies'] ?? true) ? 1 : 0 }}">
    @elseif($type === 'scene_transition')
        <input type="hidden" name="elements[{{ $index }}][scene_key]" value="{{ $element['scene_key'] ?? '' }}">
        @php($actions = collect($sceneActionOptions[$index] ?? [])->filter(fn($action) => $action === 'start' ? $canSetScene : $canEndScene))
        @if($sceneState['active'])
        <fieldset class="composer-scene-mode"><legend class="sr-only">Szene</legend>
            <label><input type="radio" name="elements[{{ $index }}][scene_action]" value="end" @checked(($element['scene_action'] ?? '') === 'end') @disabled(!$actions->contains('end'))> Szene beenden</label>
            <label><input type="radio" name="elements[{{ $index }}][scene_action]" value="start" @checked(($element['scene_action'] ?? 'start') === 'start') @disabled(!$actions->contains('start'))> Szene wechseln</label>
        </fieldset>
        @else
        <input type="hidden" name="elements[{{ $index }}][scene_action]" value="start">
        @endif
        <fieldset class="composer-scene-location" x-data="locationPicker" data-index="{{ $index }}" data-search-url="{{ route('draft.locations') }}">
            <legend>Ort</legend>
            <div class="composer-location-search"><label for="location-query-{{ $index }}" class="sr-only">Ort suchen</label><input type="search" id="location-query-{{ $index }}" name="elements[{{ $index }}][location_query]" maxlength="100" placeholder="Ort suchen" value="{{ $element['location_query'] ?? '' }}" x-ref="query" x-on:input.stop="scheduleSearch" x-on:change.stop x-on:keydown.enter.prevent="search(1)">
                <button type="submit" formnovalidate name="intent" value="location_search:{{ $index }}:1" x-on:click.prevent="search(1)" title="Orte suchen"><span class="fa fa-search" aria-hidden="true"></span></button>
            </div>
            <span role="status" x-text="status"></span>
            <div x-ref="results">@include('draft._locations', ['choices' => $locationChoices[$index], 'selected' => $element['location_id'] ?? null])</div>
        </fieldset>
        <fieldset><legend>Zeit</legend>
            <label class="sr-only" for="scene-time-{{ $index }}">Zeit</label>
            <input id="scene-time-{{ $index }}" type="datetime-local" required name="elements[{{ $index }}][story_at]" value="{{ is_numeric($element['story_at'] ?? null) ? \Illuminate\Support\Carbon::createFromTimestamp((int) $element['story_at'], config('app.timezone'))->format('Y-m-d\TH:i') : ($element['story_at'] ?? '') }}">
        </fieldset>
        <noscript><button type="submit" formnovalidate name="intent" value="save">Szenenkontext übernehmen</button></noscript>
    @elseif($type === 'poll')
        <fieldset><legend>Frage</legend>
            <label class="sr-only" for="poll-question-{{ $index }}">Frage</label>
            <input id="poll-question-{{ $index }}" name="elements[{{ $index }}][question]" maxlength="255" value="{{ $element['question'] ?? '' }}">
        </fieldset>
        <fieldset><legend>Antworten</legend>
        <ol>
        @foreach(($element['options'] ?? ['', '']) as $optionIndex => $option)
            <li>
                <label class="sr-only" for="poll-option-{{ $index }}-{{ $optionIndex }}">Antwort {{ $optionIndex + 1 }}</label>
                <input id="poll-option-{{ $index }}-{{ $optionIndex }}" name="elements[{{ $index }}][options][{{ $optionIndex }}]" maxlength="255" value="{{ $option }}">
                @if(count($element['options'] ?? []) > 2)<button type="submit" formnovalidate name="intent" value="remove_poll_option:{{ $index }}:{{ $optionIndex }}" title="Antwort entfernen" aria-label="Antwort {{ $optionIndex + 1 }} entfernen"><span class="option delete" aria-hidden="true"></span></button>@endif
            </li>
        @endforeach
        </ol>
        <button type="submit" formnovalidate name="intent" value="add_poll_option:{{ $index }}"><span class="fa fa-plus" aria-hidden="true"></span> Antwort hinzufügen</button>
        </fieldset>
        <fieldset><legend>Abstimmung</legend>
            <label><input type="radio" name="elements[{{ $index }}][visibility]" value="anonymous" @checked(($element['visibility'] ?? 'anonymous') === 'anonymous')> Anonym</label>
            <label><input type="radio" name="elements[{{ $index }}][visibility]" value="open" @checked(($element['visibility'] ?? '') === 'open')> Offen</label>
        </fieldset>
        <fieldset><legend>Auswahl und Frist</legend>
            <label>Höchstens <input type="number" min="1" max="{{ count($element['options'] ?? ['', '']) }}" name="elements[{{ $index }}][max_choices]" value="{{ $element['max_choices'] ?? 1 }}"> Antworten</label>
            <label>Abstimmungsschluss <input type="datetime-local" name="elements[{{ $index }}][closes_at]" value="{{ is_numeric($element['closes_at'] ?? null) ? \Illuminate\Support\Carbon::createFromTimestamp((int) $element['closes_at'], config('app.timezone'))->format('Y-m-d\TH:i') : ($element['closes_at'] ?? '') }}"></label>
        </fieldset>
    @elseif($type === 'transfer')
        @php($transferContext = $transferContexts[$index] ?? ['characterInventories' => collect(), 'location' => null, 'locationInventory' => collect(), 'recipients' => collect(), 'sites' => collect()])
        @php($sourceValue = $transferPickers[$index]['sourceValue'])
        <input type="hidden" name="elements[{{ $index }}][scene_key]" value="{{ $element['scene_key'] ?? '' }}">
        <div class="composer-transfer-steps">
            <div>
                <x-entity-picker :name="'elements['.$index.'][source]'" :id="'transfer-source-'.$index" legend="Von" :options="$transferPickers[$index]['sourceOptions']" :value="$sourceValue" input-class="composer-transfer-source" />
                <noscript><button type="submit" formnovalidate name="intent" value="save">Quelle übernehmen</button></noscript>
            </div>
            <div class="composer-transfer-inventories">
                @foreach($characters as $character)
                @php($inventoryIsVisible = $sourceValue === 'character' && $selectedCharacterId === $character->id)
                <fieldset class="composer-inventory" data-source="character" data-owner="{{ $character->id }}" @if(!$inventoryIsVisible) hidden @endif @disabled(!$inventoryIsVisible)><legend>{{ $character->name }}</legend>
                    @forelse($transferContext['characterInventories']->get($character->id, collect()) as $inventory)
                    <label class="composer-action-item"><input type="checkbox" name="elements[{{ $index }}][inventory][{{ $inventory->id }}]" value="{{ $inventory->id }}" @checked(isset($element['inventory'][$inventory->id])) @disabled(!$inventoryIsVisible)><x-inventory-item :inventory="$inventory" :quantity-name="'elements['.$index.'][inventorystack]['.$inventory->id.']'" :quantity-value="$element['inventorystack'][$inventory->id] ?? $inventory->makeunitary()" /></label>
                    @empty <span>Keine Gegenstände</span> @endforelse
                </fieldset>
                @endforeach
                @foreach($transferContext['sites'] as $site)
                @php($inventoryIsVisible = $sourceValue === 'company_site:'.$site->id)
                <fieldset class="composer-inventory" data-source="company_site" data-owner="{{ $site->id }}" @if(!$inventoryIsVisible) hidden @endif @disabled(!$inventoryIsVisible)><legend>{{ $site->company?->name }}@if($site->company?->sites->count() > 1) ({{ $site->name }})@endif</legend>
                    @forelse($site->inventory as $inventory)
                    <label class="composer-action-item"><input type="checkbox" name="elements[{{ $index }}][inventory][{{ $inventory->id }}]" value="{{ $inventory->id }}" @checked(isset($element['inventory'][$inventory->id])) @disabled(!$inventoryIsVisible)><x-inventory-item :inventory="$inventory" :quantity-name="'elements['.$index.'][inventorystack]['.$inventory->id.']'" :quantity-value="$element['inventorystack'][$inventory->id] ?? $inventory->makeunitary()" /></label>
                    @empty <span>Keine ausgabefähigen Gegenstände an diesem Standort</span> @endforelse
                </fieldset>
                @endforeach
                @if($transferContext['location'])
                @php($inventoryIsVisible = $sourceValue === 'location:'.$transferContext['location']->id)
                <fieldset class="composer-inventory" data-source="location" data-owner="{{ $transferContext['location']->id }}" @if(!$inventoryIsVisible) hidden @endif @disabled(!$inventoryIsVisible)><legend>{{ $transferContext['location']->name }}</legend>
                    @forelse($transferContext['locationInventory'] as $inventory)
                    <label class="composer-action-item"><input type="checkbox" name="elements[{{ $index }}][inventory][{{ $inventory->id }}]" value="{{ $inventory->id }}" @checked(isset($element['inventory'][$inventory->id])) @disabled(!$inventoryIsVisible)><x-inventory-item :inventory="$inventory" :quantity-name="'elements['.$index.'][inventorystack]['.$inventory->id.']'" :quantity-value="$element['inventorystack'][$inventory->id] ?? $inventory->makeunitary()" /></label>
                    @empty <span>Keine Gegenstände</span> @endforelse
                </fieldset>
                @endif
            </div>
            <x-entity-picker :name="'elements['.$index.'][recipient_party]'" :id="'transfer-recipient-'.$index" legend="An" :options="$transferPickers[$index]['recipientOptions']" :value="$transferPickers[$index]['recipientValue']" :searchable="true" input-class="composer-transfer-recipient" />
        </div>
    @endif
    @unless($type === 'message')
        </div>
    </details>
    @if($preview)<div class="composer-block-preview">@include('draft._preview')</div>@endif
    @endunless
    </div>
</section>
