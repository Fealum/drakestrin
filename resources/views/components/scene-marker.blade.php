@props(['label', 'location' => null, 'time' => null, 'timePrefix' => '·'])
<div class="thread-scene"><p>
    {{ $label }}
    @if($location)<a href="{{ route('location.view', $location) }}">{{ $location->name }}</a>@else Unbekannter Ort @endif
    @if($time !== null) {{ $timePrefix }} <x-datetime :time="\Illuminate\Support\Carbon::createFromTimestamp($time)" />@endif
</p></div>
