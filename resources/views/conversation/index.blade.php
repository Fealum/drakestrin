<x-main-layout title="Konversationen" css="conversation" :no-breadcrumbs="true">
    <ol id="conversations">
        @forelse ($conversations as $conversation)
        <li>
            <h4>
                @if ($conversation['latest_message']->recipient_user_id == auth()->user()->id && $conversation['latest_message']->view === 0)
                [ungelesen]
                @endif
                <a href="{{ route('user.view', $conversation['other_user']->id) }}"><x-avatar :subject="$conversation['other_user']" size="list" />{{ $conversation['other_user']->name }}</a>,
                <x-datetime :time="$conversation['latest_message']->created_at" />
                <a href="{{ url('/') }}/conversation/view/{{ $conversation['other_user']->id }}">{{ $conversation['message_count'] }} Nachricht{{ ($conversation['message_count'] === 1) ? '' : 'en' }}</a>
            </h4>
            <p>
                {!! ($conversation['latest_message']->recipient_user_id === auth()->user()->id) ? '&#11106;' : '&#10550;' !!}
                @php
                    $message = $conversation['latest_message']->message;

                    $limit = 80;
                    $end = '…';

                    $limitedMessage = Str::limit($message, $limit, '');
                    $lastSpace = strrpos($limitedMessage, ' ');
                    
                    if ($limitedMessage !== $message) {
                        if ($lastSpace !== false) {
                            $limitedMessage = substr($limitedMessage, 0, $lastSpace) . $end;
                        } else {
                            $limitedMessage = Str::limit($message, $limit, $end);
                        }
                    }
                @endphp
                {{ $limitedMessage }}

            </p>
        </li>
        @empty
        <li><p>Noch keine Konversationen!</p></li>
        @endforelse
    </ol>
</x-main-layout>
