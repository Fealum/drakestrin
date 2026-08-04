@php($poll = $element->poll)
@php($participation = auth()->check() ? $poll?->participations->firstWhere('user_id', auth()->id()) : null)
@php($showResults = $poll?->isClosed() || $participation)
@if($poll)
<section class="post-poll">
    <h4>{{ $poll->question }}</h4>
    <p>
        <small>
            {{ $poll->visibility === \App\Support\PollVisibility::OPEN ? 'Offene Abstimmung' : 'Anonyme Abstimmung' }}
            @if($poll->max_choices > 1)
                , bis zu {{ $poll->max_choices }} Antworten
            @endif
        </small>
    </p>
    @if($showResults)
        @php($total = $poll->options->sum(fn($option) => $option->voteCount()))
        <ol>
        @foreach($poll->options as $option)
            <li><strong>{{ $option->label }}</strong> · {{ $option->voteCount() }} {{ $option->voteCount() === 1 ? 'Stimme' : 'Stimmen' }}
                @if($total) ({{ number_format($option->voteCount() * 100 / $total, 1, ',', '.') }} %) @endif
                @if($poll->visibility === \App\Support\PollVisibility::OPEN && $option->participations->isNotEmpty())
                    <small>@foreach($option->participations as $vote)@if($vote->user)<a href="{{ route('user.view', $vote->user) }}">{{ $vote->user->name }}</a>@if(! $loop->last), @endif @endif @endforeach</small>
                @endif
            </li>
        @endforeach
        </ol>
    @elseif(auth()->check() && app(\App\Services\PermissionService::class)->allows('votepoll', $thread, auth()->user()))
        <form method="post" action="{{ route('poll.vote', $poll) }}">@csrf
            @foreach($poll->options as $option)
            <label><input type="{{ $poll->max_choices === 1 ? 'radio' : 'checkbox' }}" name="options[]" value="{{ $option->id }}"> {{ $option->label }}</label>
            @endforeach
            <button type="submit">Abstimmen</button>
        </form>
    @endif
    @if($poll->closes_at)<p><small>Abstimmungsschluss: <x-datetime :time="\Illuminate\Support\Carbon::createFromTimestamp($poll->closes_at)" /></small></p>@endif
</section>
@endif
