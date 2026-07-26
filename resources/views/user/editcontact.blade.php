<x-main-layout title="Kontaktmöglichkeit bearbeiten">
    <form name="editusercontact" action="{{ route('user.edit_contact', $contact->id) }}" method="post">
        @csrf
        @include('user._contact-form-fields', ['contact' => $contact, 'protocols' => $protocols])
        <p><input type="submit" value="Kontaktmöglichkeit bearbeiten" name="submit"></p>
    </form>
</x-main-layout>
