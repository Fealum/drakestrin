<x-main-layout :title="$company->name" css="company_view">
    @if ($errors->any())
    <div class="notice notice_error">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if ($canEdit)
    <p><a href="{{ route('company.edit', ['company' => $company->id]) }}">Betrieb bearbeiten</a></p>
    @endif
    <ol>
        @if ($company->character)
        <li class="owner_info">Eigentümer:
            <a href="{{ route('user.character', $company->character->id) }}">
                <x-avatar :subject="$company->character" size="dropdown" />
                {{ $company->character->name }}
            </a>
        </li>
        @endif
        @foreach ($company->sites as $site)
        @if ($site->location)
        <li class="territory_info">
            @if ($site->is_headquarters)
            Hauptsitz:
            @else
            Betriebsstätte:
            @endif
            <a href="{{ route('location.view', ['location' => $site->location->id]) }}">{{ $site->location->name }}</a>
            @if ($site->is_storefront)(Ladenlokal)@endif
        </li>
        @endif
        @endforeach
        @if ($company->sites->isEmpty() && $company->territory)
        <li class="territory_info">
            Bisheriges Gebiet:
            <a href="{{ route('territory.view', ['territory' => $company->territory->id]) }}">{{ $company->territory->displayName() }}</a>
        </li>
        @endif
        <li>Beschreibung: {{ $company->description }}</li>
        <li>
            @if ($company->workers->count() > 1)
            {{ $company->workers->count() }} Angestellte
            @elseif ($company->workers->count() === 1)
            Ein Angestellter
            @else
            Keine Angestellten
            @endif
        </li>
    </ol>

    <h3>Vertretung</h3>
    <ol>
        @if ($company->character)
        <li>{{ $company->character->name }} (Eigentümer)</li>
        @endif
        @foreach ($company->representatives as $representative)
        @if ($representative->character)
        <li>
            <a href="{{ route('user.character', ['character' => $representative->character->id]) }}">{{ $representative->character->name }}</a>
            ({{ $representative->role->label() }})
            @if ($canManageRepresentatives)
            <form action="{{ route('company.representative.destroy', ['company' => $company->id, 'representative' => $representative->id]) }}" method="post">
                @csrf
                @method('delete')
                <button type="submit">Vertretung beenden</button>
            </form>
            @endif
        </li>
        @endif
        @endforeach
    </ol>

    @if ($canManageRepresentatives)
    <form action="{{ route('company.representative.store', ['company' => $company->id]) }}" method="post">
        @csrf
        <input type="hidden" name="role" value="manager">
        <x-character-selector
            name="character_id"
            :endpoint="route('board.ajax_get_chars')"
            label="Geschäftsführung"
            placeholder="Charakter suchen ..."
        />
        <button type="submit">Geschäftsführung ernennen</button>
    </form>
    @endif

    <h3>Angestellte</h3>
    <h4>Schreiber</h4>
    <ol class="workers">
        @foreach ($company->workers->where('type', 5) as $worker)
            @include('company._worker', ['company' => $company, 'worker' => $worker, 'canManage' => $canManage, 'canPay' => $canPay])
        @endforeach
    </ol>
    @if ((int) $company->type < 5)
    <h4>Lieferanten</h4>
    <ol class="workers">
        @foreach ($company->workers->where('type', 4) as $worker)
            @include('company._worker', ['company' => $company, 'worker' => $worker, 'canManage' => $canManage, 'canPay' => $canPay])
        @endforeach
        @if ($canHire)
        <li><a href="{{ route('company.hire', ['company' => $company->id, 'type' => 4]) }}"><img src="{{ asset('images/company-worker/0.png') }}" alt="">Neuen Lieferanten einstellen</a></li>
        @endif
    </ol>
    @endif
    @if ((int) $company->type < 4)
    <h4>Handwerker</h4>
    <ol class="workers">
        @foreach ($company->workers->where('type', 3) as $worker)
            @include('company._worker', ['company' => $company, 'worker' => $worker, 'canManage' => $canManage, 'canPay' => $canPay])
        @endforeach
        @if ($canHire)
        <li><a href="{{ route('company.hire', ['company' => $company->id, 'type' => 3]) }}"><img src="{{ asset('images/company-worker/0.png') }}" alt="">Neuen Handwerker einstellen</a></li>
        @endif
    </ol>
    @endif
    @if ((int) $company->type < 3)
    <h4>{{ (int) $company->type === 1 ? 'Bergmänner' : 'Knechte' }}</h4>
    <ol class="workers">
        @foreach ($company->workers->where('type', '<', 3) as $worker)
            @include('company._worker', ['company' => $company, 'worker' => $worker, 'canManage' => $canManage, 'canPay' => $canPay])
        @endforeach
        @if ($canHire)
        <li><a href="{{ route('company.hire', ['company' => $company->id, 'type' => (int) $company->type === 1 ? 1 : 2]) }}"><img src="{{ asset('images/company-worker/0.png') }}" alt="">Neuen {{ (int) $company->type === 1 ? 'Bergmann' : 'Knecht' }} einstellen</a></li>
        @endif
    </ol>
    @endif

    @include('company._production-history', ['runs' => $company->productionRuns])

    @if ($company->inventory->isNotEmpty())
    <h3>Inventar</h3>
    <h4>Produktionsgut</h4>
    <ol class="inventory">
        @foreach ($company->inventory->where('wear', \App\Support\InventoryStockState::PRODUCTION->value) as $inventory)
            @include('company._inventory-item', ['company' => $company, 'inventory' => $inventory, 'canManage' => $canManage])
        @endforeach
    </ol>
    <h4>Vorbehaltsgut</h4>
    <ol class="inventory">
        @foreach ($company->inventory->where('wear', \App\Support\InventoryStockState::RESERVED->value) as $inventory)
            @include('company._inventory-item', ['company' => $company, 'inventory' => $inventory, 'canManage' => $canManage])
        @endforeach
    </ol>
    <h4>Verkaufsgut</h4>
    <ol class="inventory">
        @foreach ($company->inventory->where('wear', '>=', 0) as $inventory)
            @include('company._inventory-item', ['company' => $company, 'inventory' => $inventory, 'canManage' => $canManage])
        @endforeach
    </ol>
    @endif

    @include('transfer._ledger', ['transfers' => $transfers])
</x-main-layout>
