<p>Sali Vuz*,</p>
<p>Du hast Dein Passwort im <a href="{{ config('app.url') }}">{{ config('app.name') }}</a> vergessen? Das ist kein Problem!</p>
<p>Besuche einfach folgenden Link, um ein neues Passwort zu bestimmen:</p>
<p><a href="{{ route('log.new_password', ['email' => $email, 'key' => $key]) }}">{{ route('log.new_password', ['email' => $email, 'key' => $key]) }}</a></p>
<p>Solltest Du diese eMail nicht beantragt haben, bitten wir Dich, sie einfach zu ignorieren.</p>
<p>Mit besten Grüßen,</p> 
<p>die Administration</p>
<p>* Drakisch: Guten Tag.</p>