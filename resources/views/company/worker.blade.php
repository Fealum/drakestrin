<x-main-layout :title="$worker->name" css="company_view">
    <ol>
        @if ($worker->company)
        <li>Betrieb: <a href="{{ route('company.view', $worker->company->id) }}">{{ $worker->company->name }}</a></li>
        @endif
        @if ($worker->site)
        <li>Standort: {{ $worker->site->name }}</li>
        @endif
        @if ($canFire)
        <li><a href="{{ route('company.fire', $worker->id) }}">entlassen</a></li>
        @endif
        <li>
            <p>
                Eingestellt am <x-datetime :time="$worker->hired" />;
                Lohn gezahlt am <x-datetime :time="$worker->paid" />
                @if ($worker->salaryStatus())
                &mdash; {{ $worker->salaryStatus() }}
                @endif
            </p>
        </li>
        @if ((int) $worker->type === 5 && $worker->company && $worker->site)
        <li><a href="{{ route('company.pay', ['company' => $worker->company->id, 'site' => $worker->site->id]) }}">Löhne auszahlen</a></li>
        @elseif ($worker->activeLabours->isNotEmpty())
            @foreach ($worker->activeLabours as $activeLabour)
            @php($labour = $activeLabour->labour)
            @if ($labour)
            <li>
                <b>@if ($activeLabour->instances > 1){{ $activeLabour->instances }} mal @endif{{ $labour->name }}@if ($activeLabour->nextinsta != 0) (danach {{ $activeLabour->nextinsta }} Instanzen)@endif</b><br>
                Seit: <x-datetime :time="$activeLabour->since" />.<br>
                @if ($activeLabour->pause_reason === \App\Support\ProductionPauseReason::STRIKE)
                Pausiert seit: <x-datetime :time="$activeLabour->paused_at" />.<br>
                @else
                Bis: <x-datetime :time="$activeLabour->until" />.<br>
                @endif
                Anzahl:
                @if ($activeLabour->quantity === -1)
                &infin;
                @else
                Noch {{ $activeLabour->quantity === 0 ? 'dieses' : $activeLabour->quantity }} Mal.
                @endif
                <br>
                Zuweisung:
                @if ($activeLabour->prodas === \App\Support\InventoryStockState::PRODUCTION->value)
                Produktionsgut
                @elseif ($activeLabour->prodas === \App\Support\InventoryStockState::RESERVED->value)
                Vorbehaltsgut
                @else
                Verkaufsgut zum Preis von {{ \App\Support\Currency::format($activeLabour->prodas) }}
                @endif.
                @if ($activeLabour->pause_reason === \App\Support\ProductionPauseReason::STRIKE)
                <br>Wegen Streik pausiert. Die Arbeit wird nach der Lohnauszahlung fortgesetzt.
                @elseif ($activeLabour->stop_requested_at)
                <br>Wird nach diesem Durchgang beendet.
                @elseif (!$activeLabour->currentRun)
                <br>Wartet auf Rohstoffe.
                @endif
                @if ($canAssignLabour && !$activeLabour->stop_requested_at)
                <form action="{{ route('company.stop_labour', $activeLabour->id) }}" method="post">
                    @csrf
                    <button type="submit">Arbeit beenden</button>
                </form>
                @endif
            </li>
            @endif
            @endforeach
        @endif
    </ol>

    @include('company._production-history', ['runs' => $worker->productionRuns])

    @if ($canAssignLabour && $workload < 1)
    <h3>Arbeit zuweisen</h3>
    @if ($errors->any())
    <div class="notice notice_error">
        <p>Die Arbeit konnte nicht zugewiesen werden:</p>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if ($labours->isNotEmpty())
    <form name="assignlabour" action="{{ route('company.assign_labour', $worker->id) }}" method="post">
        @csrf
        <ul>
            @foreach ($labours as $labour)
            <li>
                <input name="labour" value="{{ $labour->id }}" id="labour-{{ $labour->id }}" type="radio" @checked($loop->first)>
                <label for="labour-{{ $labour->id }}">
                    <p>{{ $labour->name }}</p>
                    <p>(Dauert {{ $labour->duration }} Sekunden, maximal {{ max(1, (int) floor((1 - $workload) * $labour->workload)) }} Instanzen.)</p>
                    @if ($labour->components->isNotEmpty())
                    <p>
                        @foreach ($labour->components->where('type', 0) as $component)
                            @include('company._component-item', ['component' => $component])
                        @endforeach
                        @if ($labour->components->where('type', 1)->isNotEmpty())
                        &larr;
                        @endif
                        @foreach ($labour->components->where('type', 1) as $component)
                            @include('company._component-item', ['component' => $component])
                        @endforeach
                        @if ($labour->components->where('type', 2)->isNotEmpty())
                        &rarr;
                        @endif
                        @foreach ($labour->components->where('type', 2) as $component)
                            @include('company._component-item', ['component' => $component])
                        @endforeach
                    </p>
                    @endif
                </label>
            </li>
            @endforeach
        </ul>
        <p>
            Wie häufig soll diese Tätigkeit durchgeführt werden?<br>
            <label><input type="radio" name="quantity" value="0" checked> <input type="text" name="quantity_count" value="1"> Mal.</label><br>
            <label><input type="radio" name="quantity" value="-1"> Bis sie abgebrochen wird.</label>
        </p>
        <p>Wie viele Instanzen sollen ausgeführt werden?<br>
            <input type="text" name="instances" value="1"> Instanzen.
        </p>
        <p>Als was soll das Produkt erschaffen werden?<br>
            <label><input type="radio" name="prodas" value="{{ \App\Support\InventoryStockState::PRODUCTION->value }}" checked> Produktionsgut.</label><br>
            <label><input type="radio" name="prodas" value="{{ \App\Support\InventoryStockState::RESERVED->value }}"> Vorbehaltsgut.</label><br>
            <label for="prodas-sale"><input id="prodas-sale" type="radio" name="prodas" value="0"> Verkaufsgut zum Preis von</label>
            <x-currency-input name="prodas_value" old-key="prodas_value" :value="0" />.
        </p>
        <input type="hidden" name="assignlabour" value="1">
        <input type="submit" value="Zuweisen">
    </form>
    @else
    <p>Für diesen Arbeiter stehen derzeit keine Tätigkeiten zur Verfügung.</p>
    @endif
    @endif
</x-main-layout>
