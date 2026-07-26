@php($item = $inventory->item)
@if ($item)
<li>
    <img src="{{ asset('images/item/'.$item->img.'.png') }}" title="{{ $item->name }}" alt="">
    @if ($inventory->stack > 0)
    <div>{{ $inventory->makeunitary() }}</div>
    @endif
    @if ($canManage ?? false)
    <form action="{{ route('company.inventory.update', ['company' => $company->id, 'inventory' => $inventory->id]) }}" method="post">
        @csrf
        @method('put')
        @if ($item->stackable && $inventory->stack > 1)
        <label>
            Menge
            <input name="quantity" type="text" value="{{ $inventory->makeunitary() }}">
        </label>
        @endif
        <label>
            Verwendung
            <select name="state">
                <option value="production" @selected((int) $inventory->wear === \App\Support\InventoryStockState::PRODUCTION->value)>Produktionsgut</option>
                <option value="reserved" @selected((int) $inventory->wear === \App\Support\InventoryStockState::RESERVED->value)>Vorbehaltsgut</option>
                <option value="sale" @selected((int) $inventory->wear >= 0)>Verkaufsgut</option>
            </select>
        </label>
        <label>
            Preis in Tuk
            <input name="price" type="text" value="{{ (int) $inventory->wear >= 0 ? rtrim(rtrim(number_format($inventory->wear / 10000, 4, ',', ''), '0'), ',') : '' }}">
        </label>
        <button type="submit">Ändern</button>
    </form>
    @elseif ((int) $inventory->wear >= 0)
    <div>{{ rtrim(rtrim(number_format($inventory->wear / 10000, 4, ',', ''), '0'), ',') }} Tuk</div>
    @endif
</li>
@endif
