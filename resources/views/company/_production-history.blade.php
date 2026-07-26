@if ($runs->isNotEmpty())
<h3>Letzte Produktion</h3>
<ol class="production-history">
    @foreach ($runs as $run)
    <li>
        <x-datetime :time="$run->completed_at" />:
        {{ $run->worker_name }} &mdash; {{ $run->labour_name }}
        @if ($run->outputs)
            ({{ collect($run->outputs)->map(fn ($item) => $item['quantity'].' '.$item['name'])->join(', ') }})
        @endif
    </li>
    @endforeach
</ol>
@endif
