<x-main-layout title="Passwort vergessen" :no-breadcrumbs="true">
	<p>Um ein neues Passwort zu erhalten, gib unten die Email-Adresse ein, auf welche Dein Account registriert wurde. An diese wird sodann ein Link geschickt, mit dem Du ein neues Passwort beantragen kannst.</p>
    <p>
        <form name="forgot_password" action="{{ route('log.forgot_password') }}" method="post">
            @csrf
            <x-textinput formname="forgot_password" inputname="email" displayname="Email" maxlength="85" type="email" placeholder="email@domain.de" :value="old('email')" :autofocus="true" :required="true" />
            <input type="submit" value="Link anfordern" name="submit" />
        </form>
    </p>
</x-main-layout>