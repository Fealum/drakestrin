<x-main-layout title="Kontaktmöglichkeit löschen">
    <h3>Sicher?</h3>
    <p>Bist Du Dir sicher, dass Du die &raquo;{{ $contact->protocolModel?->name }}&laquo;-Kontaktmöglichkeit &raquo;{{ $contact->contact }}&laquo; löschen möchtest?</p>
    <form name="deletecontact" action="{{ route('user.delete_contact', $contact->id) }}" method="post">
        @csrf
        <input type="hidden" name="delete" value="1">
        <p><input type="submit" value="Kontaktmöglichkeit löschen" name="submit"></p>
    </form>
</x-main-layout>
