<x-main-layout title="Neue Kontaktmöglichkeit erstellen">
    <form name="createusercontact" action="{{ route('user.create_contact', $user->id) }}" method="post">
        @csrf
        @include('user._contact-form-fields', ['contact' => null, 'protocols' => $protocols])
        <p><input type="submit" value="Neue Kontaktmöglichkeit erstellen" name="submit"></p>
    </form>
</x-main-layout>
