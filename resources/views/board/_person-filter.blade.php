@php
    $items = collect($selectedIds)
        ->map(fn (int $id) => $lookup->get($id) ?? ['id' => $id, 'name' => '#'.$id, 'avatar' => asset('img/character_avatar.id/thumb/i/_.jpg')])
        ->values();
@endphp

<div
    class="person-filter"
    x-data="boardPersonFilter({
        endpoint: @js($endpoint),
        initialItems: @js($items),
        initialIds: @js(implode(',', $selectedIds)),
    })"
>
    <label for="{{ $name }}">{{ $label }}</label>

    <input
        class="person-filter-ids"
        type="text"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ implode(',', $selectedIds) }}"
        placeholder="IDs, durch Kommata getrennt"
        x-model="idsText"
        x-bind:name="enhanced ? null : '{{ $name }}'"
    >
    <input type="hidden" value="{{ implode(',', $selectedIds) }}" x-model="idsText" x-bind:name="enhanced ? '{{ $name }}' : null">

    <ol class="person-filter-selected" x-show="items.length > 0" x-cloak>
        <template x-for="item in items" :key="item.id">
            <li>
                <a href="#" x-on:click.prevent="remove(item.id)" title="Aus Filter entfernen">
                    <img :src="item.avatar" alt="">
                    <span x-text="item.name"></span>
                    <span aria-hidden="true">&times;</span>
                </a>
            </li>
        </template>
    </ol>

    <div class="person-filter-search" x-cloak>
        <span class="fa fa-refresh fa-spin" x-show="loading" title="Sucht ..."></span>
        <input
            type="search"
            placeholder="{{ $searchPlaceholder }}"
            x-model="query"
            x-on:input.debounce.250ms="search"
            x-on:focus="search"
        >
        <ol x-show="results.length > 0 || (query.trim().length > 0 && ! loading)">
            <template x-for="result in results" :key="result.id">
                <li>
                    <a href="#" x-on:click.prevent="add(result)">
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
