@php($transition = $element->sceneTransition)
<div class="thread-scene">
    @if($transition?->startedScene)
        <p>
            @if($transition->endedScene)
                Szene gewechselt:
            @else
                Szene:
            @endif
            @if($transition->startedScene->location)
                <a href="{{ route('location.view', $transition->startedScene->location) }}">{{ $transition->startedScene->location->name }}</a>
            @else
                Unbekannter Ort
            @endif
            @if($transition->startedScene->story_started_at)
                ab <x-datetime :time="\Illuminate\Support\Carbon::createFromTimestamp($transition->startedScene->story_started_at)" />
            @endif
        </p>
    @elseif($transition?->endedScene)
        <p>
            Szene beendet:
            @if($transition->endedScene->location)
                <a href="{{ route('location.view', $transition->endedScene->location) }}">{{ $transition->endedScene->location->name }}</a>
            @else
                Unbekannter Ort
            @endif
            @if($transition->endedScene->story_ended_at)
                um <x-datetime :time="\Illuminate\Support\Carbon::createFromTimestamp($transition->endedScene->story_ended_at)" />
            @endif
        </p>
    @endif
</div>
