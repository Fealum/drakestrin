<x-main-layout :title="'Konversation mit '. $user->name">
<form name="newMessage" action="{{ route('conversation.create', ['user' => $user->id]) }}" method="post">
    @csrf
<textarea class="textarea-bbcode" name="message"></textarea>
<input type="submit" value="Neue Nachricht erstellen" />
</form>
<ol>
@forelse ($messages as $message)
	<li>
        <h4>
        @if ($message->sender_user_id === $user->id)
        <a href="{{ url('/') }}/user/view/{{ $message->sender->id }}">{{ $message->sender->name }}</a>,
        @else
        Du,
        @endif
        <x-datetime :time="$message->created_at" />
        </h4>
	<div class="postcontent">{!! nl2br(e($message->message)) !!}</div>
	</li>
@empty
<li><p>Diese Konversation ist noch leer! Verfasse oben die erste Nachricht.</p></li>
@endforelse
</ol>
</x-main-layout>