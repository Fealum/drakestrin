@props([
    'inventory',
    'quantityName' => null,
    'quantityValue' => null,
    'showQuantity' => null,
])
@php($item = $inventory->item)
@php($showQuantity = $showQuantity ?? (bool) $item?->stackable)
@if ($item)
<span class="inventory-item-control">
    <img src="{{ asset('images/item/'.$item->img.'.png') }}" title="{{ $item->name }}" alt="{{ $item->name }}">
    @if ($quantityName && $showQuantity)
    <input
        class="inventory-item-quantity"
        name="{{ $quantityName }}"
        type="text"
        inputmode="decimal"
        aria-label="Menge von {{ $item->name }}"
        value="{{ $quantityValue ?? $inventory->makeunitary() }}"
    >
    @elseif ($showQuantity)
    <span class="inventory-item-quantity inventory-item-quantity-static">{{ $quantityValue ?? $inventory->makeunitary() }}</span>
    @endif
</span>
@endif
