<x-main-layout :title="$thread->name . ': Szene setzen'" css="thread">
    @if ($thread->currentScene)
    <p>Aktuelle Szene: {{ $thread->currentScene->location?->name }}</p>
    @endif

    <form action="{{ route('thread.scene.create', $thread) }}" method="post">
        @csrf
        <p>
            <label for="location_id">Ort</label>
            <select name="location_id" id="location_id" required>
                @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected((int) old('location_id', $thread->currentScene?->location_id) === $location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="story_started_at">Erzählzeit</label>
            <input type="datetime-local" name="story_started_at" id="story_started_at" value="{{ old('story_started_at', now()->format('Y-m-d\TH:i')) }}">
        </p>

        <input type="submit" value="Szene setzen">
    </form>
</x-main-layout>
