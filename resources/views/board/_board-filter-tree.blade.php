<ol>
@foreach ($boards as $board)
    @php($isOpen = ($showSettings ?? collect())->get($board->id, 1) !== 0)
    <li @if ($board->childBoards->isNotEmpty() && auth()->check()) x-data="{ open: @js($isOpen), loading: false }" @endif>
        <input
            type="checkbox"
            name="board[]"
            value="{{ $board->id }}"
            id="board-{{ $board->id }}"
            @checked(in_array($board->id, $selectedBoards ?? [], true))
        >
        @if ($board->childBoards->isNotEmpty() && auth()->check())
        <a
            href="{{ route('board.change_show', ['board' => $board->id]) }}"
            class="contrexp fa fa-toggle-{{ $isOpen ? 'down' : 'right' }}"
            x-bind:class="{
                'fa-toggle-down': ! loading && open,
                'fa-toggle-right': ! loading && ! open,
                'fa-refresh': loading,
                'fa-spin': loading,
            }"
            x-on:click.prevent="
                loading = true;
                $el.classList.remove('fa-toggle-down', 'fa-toggle-right');
                fetch($el.href + '/' + (open ? 0 : 1) + '/1')
                    .then((response) => response.text())
                    .then((value) => open = value.trim() !== '0')
                    .catch(() => window.location = $el.href)
                    .finally(() => loading = false)
            "
        >
            <span x-text="open ? 'verkürzen' : 'erweitern'">{{ $isOpen ? 'verkürzen' : 'erweitern' }}</span>
        </a>
        @endif
        <label for="board-{{ $board->id }}">{{ $board->name }}</label>
        @if (($viewedBoards ?? collect())->contains($board->id))
        <span class="option new">(Neu)</span>
        @endif

        @if ($board->childBoards->isNotEmpty())
            <div
                @if (auth()->check())
                x-show="open"
                @unless($isOpen) style="display: none;" @endunless
                @endif
            >
                @include('board._board-filter-tree', ['boards' => $board->childBoards, 'selectedBoards' => $selectedBoards ?? [], 'showSettings' => $showSettings ?? collect(), 'viewedBoards' => $viewedBoards ?? collect()])
            </div>
        @endif
    </li>
@endforeach
</ol>
