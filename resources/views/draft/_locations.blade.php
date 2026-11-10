<x-entity-picker :name="'elements['.$index.'][location_id]'" :id="'scene-location-'.$index" legend="Ort auswählen" :hide-legend="true" :options="$choices['options']" :value="$selected" input-class="composer-scene-place" />
@if($choices['matches']->currentPage() > 1 || $choices['matches']->hasMorePages())
<nav class="composer-location-pages" aria-label="Orte durchsuchen">
    @if($choices['matches']->currentPage() > 1)<button type="submit" formnovalidate name="intent" value="location_search:{{ $index }}:{{ $choices['matches']->currentPage() - 1 }}" x-on:click.prevent="search({{ $choices['matches']->currentPage() - 1 }})" title="Vorherige Orte"><span class="fa fa-chevron-left" aria-hidden="true"></span></button>@endif
    @if($choices['matches']->hasMorePages())<button type="submit" formnovalidate name="intent" value="location_search:{{ $index }}:{{ $choices['matches']->currentPage() + 1 }}" x-on:click.prevent="search({{ $choices['matches']->currentPage() + 1 }})" title="Weitere Orte"><span class="fa fa-chevron-right" aria-hidden="true"></span></button>@endif
</nav>
@endif
