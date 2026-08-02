<x-main-layout title="Abonnements" css="board">
    <p><a href="{{ route('forum.settings') }}" class="fa-cog"> Forum-Einstellungen</a></p>
    @if ($subscriptions->isEmpty())
    <p>Du hast derzeit keine Themen abonniert.</p>
    @else
    <form method="post" action="{{ route('subscriptions.update') }}">
        @csrf
        <ol>
            @foreach ($subscriptions as $subscription)
            <li>
                <h4><a href="{{ route('thread.view', $subscription->thread) }}">{{ $subscription->thread->name }}</a>
                    @if ($unreadIds->contains($subscription->thread_id) && $firstUnreadPosts[$subscription->thread_id])
                    <a class="option new" href="{{ route('post.view', ['post' => $firstUnreadPosts[$subscription->thread_id]]) }}">(Neu)</a>
                    @endif
                </h4>
                <p class="small">im Forum {{ $subscription->thread->board?->name }}</p>
                <label for="frequency-{{ $subscription->id }}">E-Mail-Benachrichtigung</label>
                <select id="frequency-{{ $subscription->id }}" name="subscriptions[{{ $subscription->id }}]">
                    @foreach ($frequencies as $frequency)
                    <option value="{{ $frequency->value }}" @selected($subscription->email_frequency === $frequency)>{{ $frequency->label() }}</option>
                    @endforeach
                </select>
                <input type="checkbox" id="remove-{{ $subscription->id }}" name="remove[]" value="{{ $subscription->id }}">
                <label for="remove-{{ $subscription->id }}">Abonnement beenden</label>
            </li>
            @endforeach
        </ol>
        <button type="submit" class="fa fa-save"> Abonnements speichern</button>
        <button type="reset" class="fa fa-undo"> Zurücksetzen</button>
    </form>
    @endif
</x-main-layout>
