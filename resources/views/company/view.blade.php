<x-main-layout :title="$company->name" css="company_view">
    @if ($errors->any())
    <div class="notice notice_error">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    @if ($canEdit)
    <p><a href="{{ route('company.edit', ['company' => $company->id]) }}">Betrieb bearbeiten</a></p>
    @endif

    <p>{{ $company->description }}</p>
    @if ($company->sites->count() === 1 && $company->sites->first()?->location)
    <p>Standort: <a href="{{ route('location.view', ['location' => $company->sites->first()->location_id]) }}">{{ $company->sites->first()->location->name }}</a></p>
    @endif

    <section class="company-people" aria-labelledby="company-owners-heading">
        <h3 id="company-owners-heading">Eigentümer</h3>
        <ol>
            @foreach ($company->owners as $owner)
            <li>
                <a href="{{ route('user.character', ['character' => $owner->character_id]) }}">{{ $owner->character?->name ?? 'Unbekannter Charakter' }}</a>
                @if (auth()->check() && (int) $owner->character?->user_id === (int) auth()->id() && $company->owners->count() > 1)
                <form class="company-inline-form" action="{{ route('company.owner.transfer', ['company' => $company->id, 'owner' => $owner->id]) }}" method="post">
                    @csrf
                    <label>Eigentum übertragen an
                        <select name="target_owner_id">
                            @foreach ($company->owners->where('id', '!=', $owner->id) as $targetOwner)
                            <option value="{{ $targetOwner->id }}">{{ $targetOwner->character?->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button type="submit">Übertragen</button>
                </form>
                @endif
            </li>
            @endforeach
        </ol>
        @if ($canManageOwners)
        <form action="{{ route('company.owner.store', ['company' => $company->id]) }}" method="post">
            @csrf
            <x-character-selector name="character_id" input-id="company-owner-character" :endpoint="route('board.ajax_get_chars')" label="Miteigentümer" placeholder="Charakter suchen ..." />
            <button type="submit">Miteigentümer ernennen</button>
        </form>
        @endif
    </section>

    <section class="company-people" aria-labelledby="company-representatives-heading">
        <h3 id="company-representatives-heading">Geschäftsführung</h3>
        <ol>
            @foreach ($company->representatives->where('role', \App\Support\CompanyRepresentativeRole::MANAGER) as $representative)
            <li>
                <a href="{{ route('user.character', ['character' => $representative->character_id]) }}">{{ $representative->character?->name ?? 'Unbekannter Charakter' }}</a>
                @if ($canManageManagers)
                <form class="company-inline-form" action="{{ route('company.representative.destroy', ['company' => $company->id, 'representative' => $representative->id]) }}" method="post">
                    @csrf @method('delete')
                    <button type="submit">Bevollmächtigung beenden</button>
                </form>
                @endif
            </li>
            @endforeach
        </ol>

        @if ($canManageManagers)
        <form action="{{ route('company.representative.store', ['company' => $company->id]) }}" method="post">
            @csrf
            <input type="hidden" name="role" value="manager">
            <x-character-selector name="character_id" input-id="company-manager-character" :endpoint="route('board.ajax_get_chars')" label="Geschäftsführung" placeholder="Charakter suchen ..." />
            <button type="submit">Geschäftsführung ernennen</button>
        </form>
        @endif

    </section>

    @if ($canManage)
    <details class="company-add-site">
        <summary>Weiteren Standort anlegen</summary>
        <form action="{{ route('company.site.store', ['company' => $company->id]) }}" method="post">
            @csrf
            <label>Name <input name="name" maxlength="255" required></label>
            <label>Ort
                <select name="location_id" required>
                    @foreach ($locations as $location)<option value="{{ $location['id'] }}">{{ $location['label'] }}</option>@endforeach
                </select>
            </label>
            <button type="submit">Standort anlegen</button>
        </form>
    </details>
    @endif

    @if ($company->sites->count() > 1)
    <ul class="company-site-tabs" aria-label="Standorte">@foreach ($company->sites as $site)<li><a href="#site-{{ $site->id }}">{{ $site->name }}</a></li>@endforeach</ul>
    @endif

    <div class="company-sites @if($company->sites->count() > 1) company-sites--tabs @endif">
        @foreach ($company->sites as $site)
        @include('company._site', ['site' => $site])
        @endforeach
    </div>

    @include('transfer._ledger', ['transfers' => $transfers])
</x-main-layout>
