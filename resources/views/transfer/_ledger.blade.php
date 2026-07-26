@if ($transfers->isNotEmpty())
<h3>Transaktionen</h3>
<ol class="transfer-ledger">
    @foreach ($transfers as $transfer)
    <li>
        @if ($transfer->reversal_of_transfer_id)
        <strong>Rückabwicklung:</strong>
        @endif
        @include('transfer._participant-name', ['participant' => $transfer->sender])
        &rarr;
        @foreach ($transfer->items as $transferItem)
            @if ($transferItem->item)
            {{ $transferItem->item->name }} ({{ $transferItem->item->makeunitary($transferItem->stack) }})@if (! $loop->last), @endif
            @endif
        @endforeach
        &rarr;
        @include('transfer._participant-name', ['participant' => $transfer->recipient])

        @if ($transfer->reversal)
        <small>rückgängig gemacht</small>
        @endif

        <small>
            @if ($transfer->actor)
            ausgeführt von
            <a href="{{ route('user.character', ['character' => $transfer->actor->id]) }}">{{ $transfer->actor->name }}</a>
            @endif
            @if ($transfer->story_at !== null)
            <x-datetime :time="\Illuminate\Support\Carbon::createFromTimestamp($transfer->story_at)" />
            @endif
            @if ($transfer->post)
            <a href="{{ route('post.view', ['post' => $transfer->post->id]) }}">Beitrag</a>
            @endif
            @if ($transfer->scene?->location)
            <a href="{{ route('location.view', ['location' => $transfer->scene->location->id]) }}">{{ $transfer->scene->location->name }}</a>
            @endif
        </small>
    </li>
    @endforeach
</ol>
{{ $transfers->links() }}
@endif
