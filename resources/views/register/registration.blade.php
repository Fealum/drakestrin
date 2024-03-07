<x-main-layout title="Registrieren" :no-breadcrumbs="true">
    <h3>Zweiter Schritt</h3>
    <p>Deine Email-Adresse <em>{{ $email }}</em> wurde bestätigt! Wähle nun einen Nutzernamen und ein Passwort.</p>
    <form name="register" action="{{ route('register.registration', ['email' => $email, 'key' => $key]) }}" method="post">
        @csrf
        <x-textinput formname="register" inputname="name" displayname="Nutzername" maxlength="85" :value="old('name')" :autofocus="true" :required="true" />
        <x-textinput formname="register" inputname="password" displayname="Passwort" type="password" :required="true" />
        <x-textinput formname="register" inputname="password_confirmation" displayname="Passwort wiederholen" type="password" :required="true" />
        <p><input type="checkbox" name="terms" id="terms" required/><label for="terms"> Ich erkläre mich mit den <a href="{{ route('static.terms') }}">Nutzungsbedingungen</a> einverstanden.</label>
        @error('terms')
            <span class="alert">{{ $message }}</span>
        @enderror
        </p>
        <p><input type="submit" value="Registrierung abschließen" name="submit" /></p>
    </form>
</x-main-layout>