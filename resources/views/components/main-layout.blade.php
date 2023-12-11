<!DOCTYPE html>
<html lang="de" class="no-js">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<title>{{ $title }} | {{ config('app.name') }}</title>

	<script type="module">
		document.documentElement.classList.remove('no-js');
		document.documentElement.classList.add('js');
	</script>

	<link rel="stylesheet" type="text/css" href="{{ url('/') }}/templates/standard/_css/_header{{ $headerimg }}.css" />
	<link rel="stylesheet" type="text/css" href="{{ url('/') }}/templates/standard/_css/{{ $css ?? '_' }}.css" />
    {{ $morecss ?? '' }}

	<meta name="description" content="Das Kaiserreich Drachenstein ist eine sogenannte Micronation, ein Browser-basiertes Rollenspiel, in dem ein virtueller mittelalterlicher Fantasy-Staat simuliert wird. " />
	<meta name="keywords" content="Drachenstein, Kaiserreich, Mikronation, Virtuelle Nation, Forenrollenspiel, Forumrollenspiel, Rollenspiel, Browserspiel, Browser-Rollenspiel, Mittelalter, Fantasy, Drache, kostenlos, Kaiser, Hobbit, Vampir, Elb, Elf" />

	<link rel="icon" href="{{ url('/') }}/templates/standard/_img/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="{{ url('/') }}/templates/standard/_img/favicon.ico" type="image/x-icon" />
	<script
		src="https://code.jquery.com/jquery-2.2.4.min.js"
		integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
		crossorigin="anonymous">
	</script>
	<script type="text/javascript">
	$(document).ready(function () {
		$("a#nav-open").click(function(event) {
			event.preventDefault();
			$("#nav").fadeIn();
		});
		
		$("a#nav-close").click(function(event) {
			event.preventDefault();
			$("#nav").fadeOut();
		});
		
		$("a#notifypic").click(function(event) {
			event.preventDefault();
			$("#sidebar").fadeIn();
		});
		
		$("a#sidebar-close").click(function(event) {
			event.preventDefault();
			$("#sidebar").fadeOut();
		});
	});
	</script>
{{ $js ?? '' }}
</head>
<body>
	<h1><a href="{{ url('/') }}">{{ config('app.name') }}</a></h1>
	<header>
		<a class="nav-open" id="nav-open" href="#nav">Zur Navigation</a>
		<nav id="nav">
			<ul>
				<li><a href="{{ url('/') }}/encyclopedia">Kompendium</a></li>
				<li><a href="{{ url('/') }}/board">Forum</a></li>
				<li><a href="{{ url('/') }}/user">Mitglieder</a></li> 
				<li><a href="{{ url('/') }}/territory">Atlas</a></li>
				<li><a href="{{ route('calendar') }}">Kalendarium</a></li> 
				<li><a href="{{ url('/') }}/dictionary">Diktionar</a></li>
			</ul>
			<a class="nav-close" id="nav-close" href="#nav-open">Nach oben</a>
		</nav>
		<div id="userbar">
			<div id="userbarcontent"@auth class="user-loggedin"@endauth>
			@if (Auth::check())
				<a id="notifypic" href="#sidebar"><img src="{{ url('/') }}/img/character_avatar.id/thumb/{{ auth()->user()->character__avatar ?? 0 }}.jpg" /></a>
				@isset ($newconv->data)
                <a href="{{ url('/') }}/conversation" class="fa-envelope newconv"> </a>
                @endisset
                Sali Vuz,<br />{{ auth()->user()->name }}!
			@else
				<form action="{{ url('/') }}/log/in" method="post">
					@csrf
					<input type="email" name="email" maxlength=85 placeholder="Email" tabindex=1 required>
					<input type="password" placeholder="Passwort" name="password" maxlength=85 tabindex=2 required>
					<input type="submit" class="fa-sign-in" value="Anmelden" name="submit" />
				</form>
				<a class="fa-plus-circle" href="{{ url('/') }}/register">Registrieren</a> <a class="fa-question-circle" href="{{ url('/') }}/log/forgotpw">Passwort vergessen</a>
			@endif
			</div>
		</div>
	</header>
	<main>
		{{ $patharray ?? '' }}
		<div id="breadcrumbs">
            @isset($path)
            <a href="{{ url('/') }}/">{{ config('app.name') }}</a> 
            @foreach (array_reverse($path) as $url => $name)
            / <a href="{{ url('/') }}/{{ $url }}">{{ $name }}</a>
            @endforeach
            / {{ $altTitle }}
            @endisset
        </div>
		<h2>{{ $title }}</h2>
		@foreach ($notice as $i)
		<p class="notice notice_{$i.type}">{if $i.notice != '' && file_exists("./_notice/{$i.notice}.htm")}{include "./_notice/{$i.notice}.htm"}{else}Fehler bei der Notizerstellung: {var_dump($i)}{/if}</p>
		@endforeach
		
		@foreach ($flashMessages as $i)
		<p class="notice notice_{{ $i['level'] }}">
			{!! $i['content'] !!}
		</p>
		@endforeach
		{{ $slot }}
	</main>
	<aside id="sidebar">
		@auth
		<ul id="accountoptions">
			<li><a href="{{ url('/') }}/conversation" class="fa-envelope">Konversationen</a></li>
			<li><a href="{{ url('/') }}/user/view/{{ auth()->user()->id }}" class="fa-user">Profil</a></li>
			<li><a href="{{ url('/') }}/log/out" class="fa-sign-out">Abmelden</a></li>
		</ul>
		@endauth
        @isset ($online)
		<p id="online">
			@foreach ($online as $value)
			<span>
				<a href="{{ url('/') }}/user/view/{{ $value->user }}"><img src="{{ url('/') }}/img/character_avatar.id/thumb/{{ $value->user_legacy->character__avatar ?? 0 }}.jpg" />{{ $value->user_legacy->name }}</a>, {{ $value->time->format('H:i') }}<br />
	@if ($value->controller == 'index')
		@if ($value->action == 'std')
		<a href="{{ url('/') }}/{{ $value->controller }}">Index</a>
		@elseif ($value->action == 'online')
		<a href="{{ url('/') }}/{{ $value->controller }}/{{ $value->action }}">Wer ist online?</a>
		@else
		Index, Seite <a href="{{ url('/') }}/{{ $value->controller }}/{{ $value->action }}">{{ $value->action }}</a>
		@endif
	@elseif ($value->controller == 'board')
		@if ($value->action == 'std')
		<a href="{{ url('/') }}/{{ $value->controller }}">Forenübersicht</a>
		@else
		Forum, Seite <a href="{{ url('/') }}/{{ $value->controller }}/{{ $value->action }}">{{ $value->action }}</a>
		@endif
	@elseif ($value->controller == 'thread')
		@if ($value->action == 'view')
		<a href="{{ url('/') }}/{{ $value->controller }}/{{ $value->action }}/{{ $value->location }}">Thema</a>
		@elseif ($value->action == 'create')
		Neues Thema erstellen
		@elseif ($value->action == 'edit')
		Thema »<a href="{{ url('/') }}/{{ $value->controller }}/{{ $value->action }}/{{ $value->location }}">{{ $value->location }}</a>« bearbeiten
		@elseif ($value->action == 'delete')
		Thema »<a href="{{ url('/') }}/{{ $value->controller }}/{{ $value->action }}/{{ $value->location }}">{{ $value->location }}</a>« löschen
		@else
		Thema, Seite <a href="{{ url('/') }}/{{ $value->controller }}/{{ $value->action }}">{{ $value->action }}</a>
		@endif
	@elseif ($value->controller == 'user')
		@if ($value->action == 'viewall')
		<a href="{{ url('/') }}/{{ $value->controller }}/{{ $value->action }}">Mitgliederübersicht</a>
		@elseif ($value->action == 'view')
		Mitglied »<a href="{{ url('/') }}/{{ $value->controller }}/{{ $value->action }}/{{ $value->location }}">{$value->location}</a>«
		@elseif ($value->action == 'edit')
		Mitglied »<a href="{{ url('/') }}/{{ $value->controller }}/{{ $value->action }}/{{ $value->location }}">{{ $value->location }}</a>« bearbeiten
		@else
		Mitglieder, Seite <a href="{{ url('/') }}/{{ $value->controller }}/{{ $value->action }}">{{ $value->action}}</a>
		@endif
	@elseif ($value->controller === 'StaticPage')
		@if ($value->action == 'help')
		<a href="{{ route('static.help') }}">Hilfe</a>
		@elseif ($value->action == 'terms')
		<a href="{{ route('static.terms') }}">Nutzungsbedingungen</a>
		@elseif ($value->action == 'legal')
		<a href="{{ route('static.legal') }}">Impressum</a>
		@endif
	@elseif ($value->controller)
		Seite <a href="{{ url('/') }}/{{ $value->controller }}/{{ $value->action }}">{{ $value->controller }}/{{ $value->action }}</a>
	@elseif (isset($value->route))
		Seite <a href="{{ route($value->route) }}">{{ $value->route }}</a>
	@endif
			</span>
			@endforeach
		</p>
		@endisset
		<a id="sidebar-close" href="#userbar">Nach oben</a>
	</aside>
	<footer>
        Drachenstein <span class="fa fa-paw"></span> Fabian Müller, 2003—{{ now()->year }}.<br />
        <a href="{{ route('static.help') }}">Hilfe</a>, <a href="{{ route('static.terms') }}">Nutzungsbedingungen</a>, <a href="{{ route('static.legal') }}">Impressum</a>.
    </footer>
</body>
</html>