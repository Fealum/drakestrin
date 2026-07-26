<x-main-layout :title="'Verknüpfung zwischen »'.$key->fromWord->word.'« und »'.$key->toWord->word.'« löschen'">
    <h3>Sicher?</h3>
    <p>Bist Du Dir sicher, dass Du diese Verknüpfung löschen möchtest?</p>
    <form name="deletekey" action="{{ route('dictionary.delete_key', ['key' => $key->id]) }}" method="post">
        @csrf
        <input type="hidden" name="delete" value="1" />
        <p><input type="submit" value="Eintrag löschen" name="submit" /></p>
    </form>
</x-main-layout>
