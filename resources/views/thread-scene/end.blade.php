<x-main-layout :title="$thread->name . ': Szene beenden'" css="thread">
    @if ($thread->currentScene)
    <p>Aktuelle Szene: {{ $thread->currentScene->location?->name }}</p>
    @else
    <p>Dieses Thema hat keine aktive Szene.</p>
    @endif

    <form action="{{ route('thread.scene.end', $thread) }}" method="post">
        @csrf
        <p>
            <label for="story_ended_at">Erzählzeit</label>
            <input type="datetime-local" name="story_ended_at" id="story_ended_at" value="{{ old('story_ended_at', now()->format('Y-m-d\TH:i')) }}">
        </p>

        <input type="submit" value="Szene beenden" @disabled(! $thread->currentScene)>
    </form>
</x-main-layout>
