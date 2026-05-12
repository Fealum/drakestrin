<li>
    <img src="{{ asset('images/company-worker/'.$worker->type.'.png') }}" alt="">
    <a href="{{ route('company.worker', $worker->id) }}">{{ $worker->name }}</a>
    @if ($canManage)
    (<a href="{{ route('company.fire', $worker->id) }}">entlassen</a>)
    @endif
    <p>
        Eingestellt am <x-datetime :time="$worker->hired" />;
        Lohn gezahlt am <x-datetime :time="$worker->paid" />
        @if ($worker->salaryStatus())
        &mdash; {{ $worker->salaryStatus() }}
        @endif
    </p>
    @if ((int) $worker->type === 5)
    @if ($canPay)
    <p><a href="{{ route('company.pay', $company->id) }}">Löhne auszahlen</a></p>
    @endif
    @elseif ($worker->activeLabours->isNotEmpty())
        @foreach ($worker->activeLabours as $activeLabour)
        @php($labour = $activeLabour->labourModel)
        @if ($labour)
        <p>Arbeit: {{ $labour->name }} (<x-datetime :time="$activeLabour->since" /> &mdash; <x-datetime :time="$activeLabour->until" />)</p>
        @endif
        @endforeach
    @endif
</li>
