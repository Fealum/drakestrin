<x-main-layout :title="$company->name" css="company_view">
    <ol>
        @if ($company->characterModel)
        <li class="owner_info">Eigentümer:
            <a href="{{ route('user.character', $company->characterModel->id) }}">
                <x-avatar :subject="$company->characterModel" size="dropdown" />
                {{ $company->characterModel->name }}
            </a>
        </li>
        @endif
        @if ($company->territoryModel)
        <li class="territory_info">Ort:
            <a href="{{ route('territory.view', $company->territoryModel->id) }}">
                <img src="{{ asset('images/territory/'.$company->territoryModel->id.'.png') }}" alt="Wappen von {{ $company->territoryModel->name }}">
                {{ $company->territoryModel->name }}
            </a>
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

    @if ($company->inventory->isNotEmpty())
    <h3>Inventar</h3>
    <h4>Produktionsgut</h4>
    <ol class="inventory">
        @foreach ($company->inventory->where('wear', -2) as $inventory)
            @include('company._inventory-item', ['inventory' => $inventory])
        @endforeach
    </ol>
    <h4>Vorbehaltsgut</h4>
    <ol class="inventory">
        @foreach ($company->inventory->where('wear', -1) as $inventory)
            @include('company._inventory-item', ['inventory' => $inventory])
        @endforeach
    </ol>
    <h4>Verkaufsgut</h4>
    <ol class="inventory">
        @foreach ($company->inventory->where('wear', '>=', 0) as $inventory)
            @include('company._inventory-item', ['inventory' => $inventory])
        @endforeach
    </ol>
    @endif
</x-main-layout>
