<x-main-layout :title="'Abonnenten von »'.$thread->name.'«'" css="board">
    @if ($subscribers->isEmpty())
    <p>Dieses Thema wird derzeit von niemandem abonniert.</p>
    @else
    <ul>
        @foreach ($subscribers as $subscriber)
        <li><a href="{{ route('user.view', $subscriber) }}">{{ $subscriber->name }}</a>, abonniert am <x-datetime :time="$subscriber->pivot->created_at" /></li>
        @endforeach
    </ul>
    @endif
</x-main-layout>
