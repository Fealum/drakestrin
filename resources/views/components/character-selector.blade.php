@props([
    'endpoint',
    'label',
    'name',
    'placeholder' => 'Charakter suchen ...',
])

<div
    class="character-selector"
    x-data="singleCharacterSelector({
        endpoint: @js($endpoint),
        name: @js($name),
    })"
>
    <label for="{{ $name }}">{{ $label }}</label>

    <input
        class="character-selector-id"
        type="number"
        id="{{ $name }}"
        name="{{ $name }}"
        placeholder="Charakter-ID"
        x-model="idText"
        x-bind:name="enhanced ? null : name"
    >
    <input type="hidden" x-model="idText" x-bind:name="enhanced ? name : null">

    <div class="character-selector-enhanced" x-cloak>
        <div class="character-selector-selected" x-show="selected">
            <button type="button" x-on:click="clear()">
                <img :src="selected?.avatar" alt="">
                <span x-text="selected?.name"></span>
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="character-selector-search" x-show="! selected">
            <span class="fa fa-refresh fa-spin" x-show="loading" title="Sucht ..."></span>
            <input
                type="search"
                placeholder="{{ $placeholder }}"
                x-model="query"
                x-on:input.debounce.250ms="search"
                x-on:focus="search"
            >
            <ol x-show="results.length > 0 || (query.trim().length > 0 && ! loading)">
                <template x-for="result in results" :key="result.id">
                    <li>
                        <a href="#" x-on:click.prevent="select(result)">
                            <img :src="result.avatar" alt="">
                            <span x-text="result.name"></span>
                        </a>
                    </li>
                </template>
                <li x-show="results.length === 0 && query.trim().length > 0 && ! loading">
                    <span>Nichts gefunden!</span>
                </li>
            </ol>
        </div>
    </div>
</div>
