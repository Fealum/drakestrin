<ol>
@foreach ($threads as $thread)
    <li>
        <h4>
            <a href="{{ route('thread.view', ['thread' => $thread->id]) }}">
                @if (auth()->check() && $thread->last_post_at?->timestamp >= auth()->user()->lastvisit?->timestamp && (($viewedThreads[$thread->id] ?? 0) < $thread->getRawOriginal('last_post_at')))
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
            @if ($thread->firstPost->character)
            <a href="{{ url('/user/character/'.$thread->firstPost->character->id) }}">
                <x-avatar :subject="$thread->firstPost->character" size="list" />
                {{ $thread->firstPost->character->name }}
            </a>;
            @endif
            <x-datetime :time="$thread->firstPost->time" />
            <br>
            im Forum <a href="{{ route('board.filter', ['filter' => 'board:'.$thread->board_id]) }}">{{ $thread->board?->name }}</a>
        </p>
        @endif

        @if ($thread->lastPost)
        <p class="small">
            @if ($thread->lastPost->character)
            <a href="{{ url('/user/character/'.$thread->lastPost->character->id) }}">
                <x-avatar :subject="$thread->lastPost->character" size="list" />
                {{ $thread->lastPost->character->name }}
            </a>;
            @endif
            <a href="{{ route('thread.view', ['thread' => $thread->id, 'page' => 'last']) }}#post{{ $thread->last_post_id }}">
                <x-datetime :time="$thread->last_post_at" />
            </a>
            <br>
            {{ number_format($thread->views, 0, ',', '.') }} Aufrufe und
            {{ number_format($thread->post_count, 0, ',', '.') }}
            {{ $thread->post_count === 1 ? 'Beitrag' : 'Beiträge' }}
        </p>
        @endif
    </li>
@endforeach
</ol>
