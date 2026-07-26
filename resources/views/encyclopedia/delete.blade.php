<x-main-layout :title="'Seite »'.$page->name.'« löschen'">
    <p>Bist Du Dir sicher, dass Du diese Seite löschen möchtest?</p>
    <form name="deleteEncyclopedia" action="{{ route('encyclopedia.delete', ['page' => $page->id]) }}" method="post">
        @csrf
        <p><input type="submit" value="Seite löschen" name="submit" /></p>
    </form>
</x-main-layout>