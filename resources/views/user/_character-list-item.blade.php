<li>
    <p class="small">
        <a href="{{ route('user.character', $character->id) }}">
            <x-avatar :subject="$character" size="list" />
            {{ $character->name }}
        </a>
    </p>
    <p class="small">
        <a href="{{ url('/board/filter/char_contains:'.$character->id) }}">{{ number_format($character->post__total ?? 0, 0, ',', '.') }} Beiträge</a>
        seit <x-datetime :time="$character->regdate" only-date="1" />
    </p>
</li>
