<x-main-layout title="Passwort vergessen" :no-breadcrumbs="true">
    <h3>Fast geschafft!</h3>
	<p>Es wurde ein Link zur Passwortänderung an die angegebene Email-Adresse verschickt, falls dazu ein passendes Nutzerkonto in unserem System existiert.</p>
    <p>Solltest Du keine Email bekommen haben, wende Dich gerne an die Administration unter <a href="mailto:{{ config('app.admin_mail') }}">{{ config('app.admin_mail') }}</a>.</p>
</x-main-layout>