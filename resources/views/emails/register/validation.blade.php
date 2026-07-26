<p>Sali Vuz*,</p>
<p>wir freuen uns, dass Du Dich entschieden hast, Dich im <a href="{{ config('app.url') }}">{{ config('app.name') }}</a> zu registrieren!</p>
<p>Um die Registrierung zu beginnen, ist es zuerst notwendig, Deine Email-Adresse zu bestätigen. Hierzu bitten wir Dich, folgenden Link zu besuchen:</p>
<p><a href="{{ route('register.registration', ['email' => $email, 'key' => $key]) }}">{{ route('register.registration', ['email' => $email, 'key' => $key]) }}</a></p>
<p>Der Link ist {{ config('auth.valid_email_timeout') / 3600 }} Stunden gültig. Aber keine Sorge: Sollte er verfallen, kannst Du jederzeit einen neuen beantragen.</p>
<p>Solltest Du diese Email nicht beantragt haben, bitten wir Dich, sie einfach zu ignorieren – es sei denn, Du hast nun Interesse bekommen, dann freuen wir uns auf Dich!</p>
<p>Mit besten Grüßen,</p> 
<p>die Administration</p>
<p>* Drakisch: Guten Tag.</p>