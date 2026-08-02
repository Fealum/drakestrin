<x-main-layout title="Registrieren" :no-breadcrumbs="true">
    <h3>Geschafft!</h3>
    <p>Die Registrierung war erfolgreich! Herzlich Willkommen im {{ config('app.name') }}. Was Du jetzt machen kannst?</p>
    <p>
        <ul>
			<li>Dein <a href="{{ route('user.view', auth()->user()) }}">Profil verschönern</a> …</li>
			<li>Deine Einstellungen an Deine Wünsche anpassen …</li>
			<li>Einen ersten <a href="{{ route('thread.create') }}">Beitrag schreiben</a> …</li>
		</ul>
	</p>
</x-main-layout>
