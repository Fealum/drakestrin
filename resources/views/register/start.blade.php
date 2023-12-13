<x-main-layout title="Registrieren">
    <h3>Erster Schritt</h3>
    <p>Die Registrierung im {{ config('app.name') }} verläuft in zwei Schritten: 
        <ol>
            <li>Zuerst wird überprüft, ob Deine Email-Adresse korrekt ist. Ohne korrekte Email-Adresse wären wichtige Funktionen des Forums wie administrative Benachrichtigungen oder Hilfe bei einem vergessenen Passwort nicht möglich.</li>
            <li>Anschließend kannst Du Deinen gewünschten Nutzernamen und Dein Passwort wählen.</li>
        </ol>
        Fangen wir also mit Deiner Email-Adresse an!
    </p>
    <p>
        <form name="register" action="{{ route('register.validation') }}" method="post">
            @csrf
            <x-textinput formname="register" inputname="email" displayname="Email" maxlength="85" type="email" placeholder="email@domain.de" :value="old('email')" :autofocus="true" :required="true" />
            <input type="submit" value="Los!" name="submit" />
        </form>
    </p>
</x-main-layout>