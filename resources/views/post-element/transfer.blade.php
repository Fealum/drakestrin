@php($transfer = $element->transfer)
@if($transfer)
<div class="post-transfer">
    @if ($transfer->reversal_of_transfer_id)<strong>Rückabwicklung:</strong>@endif
    @include('transfer._participant', ['participant' => $transfer->sender])
    &rarr;
    @foreach ($transfer->items as $transferItem)
        @if ($transferItem->item) {{ $transferItem->item->name }} ({{ $transferItem->item->makeunitary($transferItem->stack) }}) @endif
    @endforeach
    &rarr;
    @include('transfer._participant', ['participant' => $transfer->recipient])
    @if ($transfer->reversal)<small>rückgängig gemacht</small>@endif
    @if ($reversibleTransferIds->contains($transfer->id))
    <form action="{{ route('transfer.reverse', $transfer) }}" method="post" class="transfer-reversal-form">@csrf<button type="submit" onclick="return confirm('Diese Handlung wirklich rückgängig machen?')">rückgängig machen</button></form>
    @endif
</div>
@endif
