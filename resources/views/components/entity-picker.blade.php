@props([
    'name',
    'id',
    'legend',
    'options' => [],
    'value' => null,
    'searchable' => false,
    'inputClass' => '',
    'hideLegend' => false,
])

<fieldset {{ $attributes->class(['entity-picker']) }} x-data="entityPicker" x-on:change="filter()" x-on:entity-options-updated="filter()">
    @if($hideLegend)
    <legend class="sr-only">{{ $legend }}</legend>
    @else
    <legend>{{ $legend }}</legend>
    @endif
    @if($searchable)
    <template x-if="available > 6">
        <input type="search" class="entity-picker-search" aria-label="{{ $legend }} durchsuchen" placeholder="Suchen …" autocomplete="off" x-on:input.stop="query = $event.target.value; filter()" x-on:change.stop x-on:keydown.enter.prevent>
    </template>
    @endif
    <div class="entity-picker-options" x-ref="choices">
        @foreach($options as $option)
        <label class="entity-picker-option" data-search="{{ $option['label'] }}" @if($option['disabled'] ?? false) hidden @endif>
            <input type="radio" name="{{ $name }}" id="{{ $id }}-{{ $loop->index }}" value="{{ $option['value'] }}" class="{{ $inputClass }}" @checked((string) $value === (string) $option['value']) @disabled($option['disabled'] ?? false)
                @if(isset($option['character_ids'])) data-character-ids="{{ implode(',', $option['character_ids']) }}" @endif
                @if(isset($option['stock_character_ids'])) data-stock-character-ids="{{ implode(',', $option['stock_character_ids']) }}" @endif
                @if($option['current_character'] ?? false) data-current-character @endif>
            @if($option['avatar'] ?? null)
            <img class="entity-picker-avatar" src="{{ $option['avatar'] }}" alt="" loading="lazy">
            @else
            <span class="entity-picker-icon fa fa-{{ $option['icon'] ?? 'user' }}" aria-hidden="true"></span>
            @endif
            <span class="entity-picker-name">{{ $option['label'] }}</span>
        </label>
        @endforeach
    </div>
    <template x-if="true"><span class="entity-picker-empty" role="status" x-show="matches === 0" x-text="query ? 'Keine Treffer.' : 'Keine Auswahl möglich.'">Keine Auswahl möglich.</span></template>
</fieldset>
