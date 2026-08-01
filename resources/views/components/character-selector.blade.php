@props([
    'endpoint',
    'inputId' => null,
    'label',
    'name',
    'placeholder' => 'Charakter suchen ...',
])

@php($inputId ??= $name)

@once
<script defer src="{{ asset('js/alpine.min.js') }}"></script>
<script>
    window.singleCharacterSelector = function ({ endpoint, name }) {
        return {
            endpoint,
            enhanced: false,
            idText: '',
            loading: false,
            name,
            query: '',
            results: [],
            selected: null,
            init() {
                this.enhanced = true;
            },
            clear() {
                this.selected = null;
                this.idText = '';
                this.query = '';
                this.results = [];
            },
            select(character) {
                this.selected = character;
                this.idText = character.id;
                this.query = '';
                this.results = [];
            },
            search() {
                if (this.query.trim().length < 1) {
                    this.results = [];
                    this.loading = false;
                    return;
                }

                this.loading = true;
                fetch(this.endpoint + '?q=' + encodeURIComponent(this.query))
                    .then((response) => response.json())
                    .then((results) => this.results = results)
                    .catch(() => this.results = [])
                    .finally(() => this.loading = false);
            },
        };
    };
</script>
@endonce

<div
    class="character-selector"
    x-data="singleCharacterSelector({
        endpoint: @js($endpoint),
        name: @js($name),
    })"
>
    <label for="{{ $inputId }}">{{ $label }}</label>

    <input
        class="character-selector-id"
        type="number"
        id="{{ $inputId }}"
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
