<x-main-layout :title="$location->name">
    @if ($location->parent)
    <p>
        Gehört zu:
        @if ($location->parent instanceof \App\Models\Territory\Territory)
        <a href="{{ route('territory.view', $location->parent) }}">{{ $location->parent->displayName() }}</a>
        @elseif ($location->parent instanceof \App\Models\Territory\Settlement)
        {{ $location->parent->name }}
        @elseif ($location->parent instanceof \App\Models\Territory\Location)
        <a href="{{ route('location.view', $location->parent) }}">{{ $location->parent->name }}</a>
        @else
        {{ $location->parent->name ?? 'Unbekannt' }}
        @endif
    </p>
    @endif

    @if ($location->description)
    <p>{{ $location->description }}</p>
    @endif

    @if ($canEdit || $canDelete || $canCreateChild)
    <p>
        @if ($canCreateChild)
        <a href="{{ route('location.create', ['parentType' => 'location', 'parentId' => $location->id]) }}">Unterort erstellen</a>
        @endif
        @if ($canEdit)
        <a href="{{ route('location.edit', $location) }}">bearbeiten</a>
        @endif
        @if ($canDelete)
        <a href="{{ route('location.delete', $location) }}">löschen</a>
        @endif
    </p>
    @endif

    <ol>
        @if ($location->creator)
        <li>Erstellt von: <a href="{{ route('user.view', $location->creator) }}">{{ $location->creator->name }}</a></li>
        @endif
        @if ($location->created_at)
        <li>Erstellt: <x-datetime :time="$location->created_at" /></li>
        @endif
        @if ($location->updated_at && $location->updated_at->ne($location->created_at))
        <li>Geändert: <x-datetime :time="$location->updated_at" /></li>
        @endif
    </ol>

    @if ($location->children->isNotEmpty())
    <h3>Orte</h3>
    <ol>
        @include('location._children', ['locations' => $location->children])
    </ol>
    @endif

    @if ($location->inventory->isNotEmpty())
    <h3>Gegenstände</h3>
    <ul class="location-inventory">
        @foreach ($location->inventory as $inventory)
        @if ($inventory->item)
        <li>{{ $inventory->item->name }} ({{ $inventory->makeunitary() }})</li>
        @endif
        @endforeach
    </ul>
    @endif

    @if ($location->threadScenes->isNotEmpty())
    <h3>Szenen</h3>
    <ol>
        @foreach ($location->threadScenes as $scene)
        @if ($scene->thread)
        <li>
            <a href="{{ route('thread.view', $scene->thread) }}">{{ $scene->thread->name }}</a>
            @if ($scene->isActive())
            <small>aktiv</small>
            @endif
        </li>
        @endif
        @endforeach
    </ol>
    @endif

    @if ($location->companySites->isNotEmpty())
    <h3>Betriebe</h3>
    <ol>
        @foreach ($location->companySites as $site)
        @if ($site->company)
        <li>
            <a href="{{ route('company.view', $site->company) }}">{{ $site->company->name }}</a>
            @if ((int) $site->company?->headquarters_site_id === (int) $site->id)(Hauptsitz)@endif
        </li>
        @endif
        @endforeach
    </ol>
    @endif

    @include('transfer._ledger', ['transfers' => $transfers])
</x-main-layout>
