@php($implicitSite = $company->sites->count() === 1 && $site->location_id !== null)
<section class="company-site-panel" @unless($implicitSite) id="site-{{ $site->id }}" aria-labelledby="site-{{ $site->id }}-heading" @endunless>
    @unless ($implicitSite)
        <h3 class="company-site-heading" id="site-{{ $site->id }}-heading">{{ $site->name }}</h3>
        @if ((int) $company->headquarters_site_id === (int) $site->id)<span>Hauptsitz</span>@endif

    @if ($site->location)
    <p>Ort: <a href="{{ route('location.view', $site->location) }}">{{ $site->location->name }}</a></p>
    @else
    <div class="notice notice_error">Für diesen übernommenen Standort muss noch ein Ort festgelegt werden.</div>
    @endif

    @can('update', $site)
    <details class="company-site-settings" @if(!$site->location_id) open @endif>
        <summary>Standort verwalten</summary>
        <form action="{{ route('company.site.update', ['company' => $company, 'site' => $site]) }}" method="post">
            @csrf @method('put')
            <label>Name <input name="name" value="{{ old('name', $site->name) }}" maxlength="255" required></label>
            <label>Ort
                <select name="location_id" required>
                    @foreach ($locations as $location)
                    <option value="{{ $location['id'] }}" @selected((int) $site->location_id === (int) $location['id'])>{{ $location['label'] }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit">Standort speichern</button>
        </form>
        @if ((int) $company->headquarters_site_id !== (int) $site->id && $site->location_id)
        <form action="{{ route('company.site.headquarters', ['company' => $company, 'site' => $site]) }}" method="post">
            @csrf @method('patch')
            <button type="submit">Zum Hauptsitz bestimmen</button>
        </form>
        <form action="{{ route('company.site.destroy', ['company' => $company, 'site' => $site]) }}" method="post">
            @csrf @method('delete')
            <button type="submit">Standort löschen</button>
        </form>
        @endif
    </details>
    @endcan
    @endunless

    <section class="company-site-representatives" aria-labelledby="site-{{ $site->id }}-representatives-heading">
        <h4 id="site-{{ $site->id }}-representatives-heading">Standortvertretung</h4>
        @if ($site->representatives->isNotEmpty())
        <ol>
            @foreach ($site->representatives as $representative)
            <li>
                <a href="{{ route('user.character', $representative->character) }}">{{ $representative->character?->name ?? 'Unbekannter Charakter' }}</a>
                ({{ $representative->role->label() }})
                @if ($canManageSiteRepresentatives)
                <form class="company-inline-form" action="{{ route('company.representative.destroy', ['company' => $company, 'representative' => $representative]) }}" method="post">
                    @csrf @method('delete')
                    <button type="submit">Bevollmächtigung beenden</button>
                </form>
                @endif
            </li>
            @endforeach
        </ol>
        @endif

        @if ($canManageSiteRepresentatives)
        <form action="{{ route('company.representative.store', $company) }}" method="post">
            @csrf
            <input type="hidden" name="company_site_id" value="{{ $site->id }}">
            <x-character-selector name="character_id" input-id="site-{{ $site->id }}-representative-character" :endpoint="route('board.ajax_get_chars')" label="Standortvertretung" placeholder="Charakter suchen ..." />
            <label>Aufgabe
                <select name="role">
                    <option value="foreman">Vorarbeiter</option>
                    <option value="clerk">Verkäufer</option>
                </select>
            </label>
            <button type="submit">Bevollmächtigen</button>
        </form>
        @endif
    </section>

    @php($canManageWorkers = auth()->check() && auth()->user()->can('manageWorkers', $site))
    <h4>Angestellte</h4>
    @php($workerGroups = [5 => 'Schreiber'])
    @if ((int) $company->type < 5) @php($workerGroups[4] = 'Lieferanten') @endif
    @if ((int) $company->type < 4) @php($workerGroups[3] = 'Handwerker') @endif
    @if ((int) $company->type < 3) @php($workerGroups[(int) $company->type === 1 ? 1 : 2] = (int) $company->type === 1 ? 'Bergmänner' : 'Knechte') @endif
    @foreach ($workerGroups as $type => $label)
        <h5>{{ $label }}</h5>
        <ol class="workers">
            @foreach ($site->workers->where('type', $type) as $worker)
            @include('company._worker', ['company' => $company, 'site' => $site, 'worker' => $worker, 'canManageWorkers' => $canManageWorkers])
            @endforeach
            @if ($canManageWorkers && $site->location_id)
            <li><a href="{{ route('company.hire', ['company' => $company, 'site' => $site, 'type' => $type]) }}">Neuen Beschäftigten einstellen</a></li>
            @endif
        </ol>
    @endforeach

    @include('company._production-history', ['runs' => $site->productionRuns])

    @php($inventoryGroups = $site->inventory_groups)
    @php($canViewInventory = auth()->check() && auth()->user()->can('viewInventory', $site))
    @php($inventoryColumns = [
        ['state' => 'production', 'title' => 'Produktionsgüter', 'description' => 'Werden zur Herstellung anderer Güter verwendet.', 'icon' => 'icon-production', 'groups' => $inventoryGroups->filter(fn ($group) => $group->first()?->stockState() === \App\Support\InventoryStockState::PRODUCTION)],
        ['state' => 'reserved', 'title' => 'Vorbehaltsgüter', 'description' => 'Werden für besondere Zwecke vorbehalten.', 'icon' => 'icon-reserved', 'groups' => $inventoryGroups->filter(fn ($group) => $group->first()?->stockState() === \App\Support\InventoryStockState::RESERVED)],
        ['state' => 'sale', 'title' => 'Verkaufsgüter', 'description' => 'Werden zu einem gesetzten Preis frei verkauft.', 'icon' => 'icon-sale', 'groups' => $inventoryGroups->filter(fn ($group) => $group->first()?->isForSale())],
    ])
    @if ($canViewInventory && $inventoryGroups->isNotEmpty())
    <h4>Inventar</h4>
    @php($canManageInventory = auth()->check() && auth()->user()->can('manageInventory', $site))
    @if ($canManageInventory)
    <form class="company-inventory-form" action="{{ route('company.inventory.update', ['company' => $company, 'site' => $site]) }}" method="post">
        @csrf @method('put')
    @endif
        <div class="company-inventory-columns">
            @foreach ($inventoryColumns as $column)
            <section class="company-inventory-column" data-inventory-state="{{ $column['state'] }}">
                <h5><svg class="company-inventory-heading-icon" aria-hidden="true"><use href="{{ asset('css/img/company_icons.svg') }}#{{ $column['icon'] }}"></use></svg>{{ $column['title'] }}</h5>
                <small>{{ $column['description'] }}</small>
                <ol class="company-inventory">
                    @foreach ($column['groups'] as $inventoryGroup)
                    @include('company._inventory-item', ['company' => $company, 'inventory' => $inventoryGroup->first(), 'members' => $inventoryGroup, 'canManage' => $canManageInventory])
                    @endforeach
                </ol>
            </section>
            @endforeach
        </div>
    @if ($canManageInventory)
        <p class="company-inventory-actions"><button class="company-inventory-reset" type="reset">Zurücksetzen</button><button class="company-inventory-submit" type="submit">Inventar speichern</button></p>
    </form>
    @endif
    @endif
</section>
