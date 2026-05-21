<x-main-layout :title="$location->name . ' löschen'">
    @if ($errors->any())
    <ul class="errors">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif

    <p>Soll der Ort &raquo;{{ $location->name }}&laquo; wirklich gelöscht werden?</p>

    @if ($location->children->isNotEmpty())
    <p>Dieser Ort enthält Unterorte und kann nicht gelöscht werden.</p>
    @endif

    @if ($location->inventory->isNotEmpty())
    <p>Dieser Ort enthält Inventar und kann nicht gelöscht werden.</p>
    @endif

    <form action="{{ route('location.delete', ['location' => $location->id]) }}" method="post">
        @csrf
        <input type="submit" value="Ort löschen" @disabled($location->children->isNotEmpty() || $location->inventory->isNotEmpty())>
    </form>
</x-main-layout>
