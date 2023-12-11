<x-main-layout title="Registrieren">
    <h3>Erster Schritt getan!</h3>
	<p>Wenn sich kein Fehler eingeschlichen hat, wurde Dir soeben eine Email an die angegebene Adresse <em>{{ $email }}</em> zugestellt. In dieser ist ein für {{ config('auth.valid_email_timeout') / 3600 }} Stunden gültiger Link enthalten, mit dem Du Deine Email-Adresse bestätigen kannst.</p>
	<p>Nach der Bestätigung kannst Du mit der Registrierung fortfahren.</p>
</x-main-layout>