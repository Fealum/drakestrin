<ol>
@foreach ($threads as $thread)
    <li>
        <h4>
            <a href="{{ route('thread.view', ['thread' => $thread->id]) }}">
                @if (auth()->check() && $thread->post__last_time?->timestamp >= auth()->user()->lastvisit?->timestamp && (($viewedThreads[$thread->id] ?? 0) < $thread->getRawOriginal('post__last_time')))
                <span class="option new">(Neu)</span>
                @endif
                @if ($thread->important)
                <span class="important">Wichtig</span>
                @endif
                {{ $thread->name }}
            </a>
        </h4>

        @if ($thread->firstPost)
        <p class="small">
            @if ($thread->firstPost->characterModel)
            <a href="{{ url('/user/character/'.$thread->firstPost->characterModel->id) }}">
                <x-avatar :subject="$thread->firstPost->characterModel" size="list" />
                {{ $thread->firstPost->characterModel->name }}
            </a>;
            @endif
            <x-datetime :time="$thread->firstPost->time" />
            <br>
            im Forum <a href="{{ route('board.filter', ['filter' => 'board:'.$thread->board]) }}">{{ $thread->boardModel?->name }}</a>
        </p>
        @endif

        @if ($thread->lastPost)
        <p class="small">
            @if ($thread->lastPost->characterModel)
            <a href="{{ url('/user/character/'.$thread->lastPost->characterModel->id) }}">
                <x-avatar :subject="$thread->lastPost->characterModel" size="list" />
                {{ $thread->lastPost->characterModel->name }}
            </a>;
            @endif
            <a href="{{ route('thread.view', ['thread' => $thread->id, 'page' => 'last']) }}#post{{ $thread->post__last }}">
                <x-datetime :time="$thread->post__last_time" />
            </a>
            <br>
            {{ number_format($thread->views, 0, ',', '.') }} Aufrufe und
            {{ number_format($thread->post__total, 0, ',', '.') }}
            {{ $thread->post__total === 1 ? 'Beitrag' : 'Beiträge' }}
        </p>
        @endif
    </li>
@endforeach
</ol>
