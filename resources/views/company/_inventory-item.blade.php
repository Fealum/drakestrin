@php($item = $inventory->itemModel)
@if ($item)
<li>
    <img src="{{ asset('images/item/'.$item->img.'.png') }}" title="{{ $item->name }}" alt="">
    @if ($inventory->stack > 0)
    <div>{{ $inventory->makeunitary() }}</div>
    @endif
</li>
@endif
