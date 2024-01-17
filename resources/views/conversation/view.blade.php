<x-main-layout :title="'Konversation mit '. $user->name">
<form name="newMessage" action="{{ route('conversation.create', ['user' => $user->id]) }}" method="post">
    @csrf
<textarea class="textarea-bbcode" name="message"></textarea>
<input type="submit" value="Neue Nachricht erstellen" />
</form>
<ol>
@forelse ($conversations as $conversation)
	<li>
        <h4>
        @if ($conversation->user__sender === $user->id)
        <a href="{{ url('/') }}/user/view/{{ $conversation->sender->id }}">{{ $conversation->sender->name  }}</a>,
        @else
        Du,
        @endif
        <x-datetime :time="$conversation->time" />
        </h4>
	<div class="postcontent">{{ $conversation->message }}</div>
	</li>
@empty
<li><p>Diese Konversation ist noch leer! Verfasse oben die erste Nachricht.</p></li>
@endforelse
</ol>
</x-main-layout>