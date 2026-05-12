<x-main-layout :title="$worker->name" css="company_view">
    <ol>
        @if ($worker->companyModel)
        <li>Betrieb: <a href="{{ route('company.view', $worker->companyModel->id) }}">{{ $worker->companyModel->name }}</a></li>
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
        @if ((int) $worker->type === 5 && $worker->companyModel)
        <li><a href="{{ route('company.pay', $worker->companyModel->id) }}">Löhne auszahlen</a></li>
        @elseif ($worker->activeLabours->isNotEmpty())
            @foreach ($worker->activeLabours as $activeLabour)
            @php($labour = $activeLabour->labourModel)
            @if ($labour)
            <li>
                <b>@if ($activeLabour->instances > 1){{ $activeLabour->instances }} mal @endif{{ $labour->name }}@if ($activeLabour->nextinsta != 0) (danach {{ $activeLabour->nextinsta }} Instanzen)@endif</b><br>
                Seit: <x-datetime :time="$activeLabour->since" />.<br>
                Bis: <x-datetime :time="$activeLabour->until" />.<br>
                Anzahl:
                @if ($activeLabour->quantity === -1)
                &infin;
                @else
                Noch {{ $activeLabour->quantity === 0 ? 'dieses' : $activeLabour->quantity }} Mal.
                @endif
                <br>
                Zuweisung:
                @if ($activeLabour->prodas === -2)
                Produktionsgut
                @elseif ($activeLabour->prodas === -1)
                Vorbehaltsgut
                @else
                Verkaufsgut zum Preis von {{ $activeLabour->prodas }} Ten
                @endif.
            </li>
            @endif
            @endforeach
        @endif
    </ol>

    @if ($canAssignLabour && $workload < 1)
    <h3>Arbeit zuweisen</h3>
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
            <label><input type="radio" name="prodas" value="-2" checked> Produktionsgut.</label><br>
            <label><input type="radio" name="prodas" value="-1"> Vorbehaltsgut.</label><br>
            <label><input type="radio" name="prodas" value="0"> Verkaufsgut zum Preis von <input type="text" name="prodas_value" value=""> Tuk.</label>
        </p>
        <input type="hidden" name="assignlabour" value="1">
        <input type="submit" value="Zuweisen">
    </form>
    @else
    <p>Für diesen Arbeiter stehen derzeit keine Tätigkeiten zur Verfügung.</p>
    @endif
    @endif
</x-main-layout>
