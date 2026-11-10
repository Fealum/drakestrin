@php($transition = $element->sceneTransition)
@if($transition?->startedScene)
    <x-scene-marker :label="$transition->endedScene ? 'Szene gewechselt:' : 'Szene:'" :location="$transition->startedScene->location" :time="$transition->startedScene->story_started_at" time-prefix="ab" />
@elseif($transition?->endedScene)
    <x-scene-marker label="Szene beendet:" :location="$transition->endedScene->location" :time="$transition->endedScene->story_ended_at" time-prefix="um" />
@endif
