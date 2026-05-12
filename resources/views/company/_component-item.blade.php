@php($item = $component->itemModel)
@if ($item)
<span class="component-item">
    <img src="{{ asset('images/item/'.$item->img.'.png') }}" title="{{ $item->name }}" alt="">
    @if ($component->quantity > 1)
    {{ $component->quantity }} ×
    @endif
    {{ $item->name }}
</span>
@endif
