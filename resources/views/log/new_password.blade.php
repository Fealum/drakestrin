<x-main-layout title="Neues Passwort setzen" :no-breadcrumbs="true">
	<p>Bitte trage nun Dein neues Passwort ein.</p>
    <p>
        <form name="new_password" action="{{ route('log.new_password', ['email' => $email, 'key' => $key]) }}" method="post">
            @csrf
            <x-textinput formname="new_password" inputname="password" displayname="Passwort" type="password" :autofocus="true" :required="true" />
            <x-textinput formname="new_password" inputname="password_confirmation" displayname="Passwort wiederholen" type="password" :autofocus="true" :required="true" />
            <input type="submit" value="Passwort ändern" name="submit" />
        </form>
    </p>
</x-main-layout>