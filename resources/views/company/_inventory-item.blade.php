@php($item = $inventory->item)
@php($members = $members ?? collect([$inventory]))
@php($displayQuantity = $item?->stackable ? $inventory->makeunitary() : $members->count())
@if ($item)
<li class="company-inventory-row">
    <x-inventory-item
        :inventory="$inventory"
        :quantity-name="($canManage ?? false) ? 'inventory['.$inventory->id.'][quantity]' : null"
        :quantity-value="old('inventory.'.$inventory->id.'.quantity', $displayQuantity)"
        :show-quantity="true"
    />
    @if (($canManage ?? false) && ! $item->stackable)
        @foreach ($members as $member)
        <input name="inventory[{{ $inventory->id }}][members][]" type="hidden" value="{{ $member->id }}">
        @endforeach
    @endif
    @if ($canManage ?? false)
    @php($selectedState = old('inventory.'.$inventory->id.'.state', $inventory->isForSale() ? 'sale' : ($inventory->stockState() === \App\Support\InventoryStockState::RESERVED ? 'reserved' : 'production')))
    <ul>
        <li>
            <input type="radio" name="inventory[{{ $inventory->id }}][state]" id="inventory-{{ $inventory->id }}-state-production" value="production" @checked($selectedState === 'production')/>
            <label for="inventory-{{ $inventory->id }}-state-production" title="Produktionsgut">
                <svg class="inventory-icon" aria-hidden="true">
                    <use href="{{ asset('css/img/company_icons.svg') }}#icon-production"></use>
                </svg>
                <span class="sr-only">Produktionsgut</span>
            </label>
        </li>
        <li>
            <input type="radio" name="inventory[{{ $inventory->id }}][state]" id="inventory-{{ $inventory->id }}-state-reserved" value="reserved" @checked($selectedState === 'reserved')/>
            <label for="inventory-{{ $inventory->id }}-state-reserved" title="Vorbehaltsgut">
                <svg class="inventory-icon" aria-hidden="true">
                    <use href="{{ asset('css/img/company_icons.svg') }}#icon-reserved"></use>
                </svg>
                <span class="sr-only">Vorbehaltsgut</span>
            </label>
        </li>
        <li>
            <input type="radio" name="inventory[{{ $inventory->id }}][state]" id="inventory-{{ $inventory->id }}-state-sale" value="sale" @checked($selectedState === 'sale')/>
            <label for="inventory-{{ $inventory->id }}-state-sale" title="Verkaufsgut">
                <svg class="inventory-icon" aria-hidden="true">
                    <use href="{{ asset('css/img/company_icons.svg') }}#icon-sale"></use>
                </svg>
                <span class="sr-only">Verkaufsgut</span>
            </label>
            <x-currency-input
                :name="'inventory['.$inventory->id.'][price]'"
                :old-key="'inventory.'.$inventory->id.'.price'"
                :value="$inventory->isForSale() ? $inventory->wear : 0"
            />
        </li>
    </ul>
    @else
    <span class="company-inventory-state">
        @if ($inventory->isForSale())
        Verkaufsgut · {{ \App\Support\Currency::format($inventory->wear) }}
        @elseif ($inventory->stockState() === \App\Support\InventoryStockState::RESERVED)
        Vorbehaltsgut
        @else
        Produktionsgut
        @endif
    </span>
    @endif
</li>
@endif
