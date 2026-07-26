<x-main-layout title="Forum" css="board">
    <x-slot:js>
        <script defer src="{{ asset('js/alpine.min.js') }}"></script>
        <script>
            window.boardPersonFilter = function ({ endpoint, initialItems, initialIds }) {
                return {
                    endpoint,
                    enhanced: false,
                    idsText: initialIds,
                    items: initialItems,
                    loading: false,
                    query: '',
                    results: [],
                    init() {
                        this.enhanced = true;
                    },
                    add(item) {
                        if (! this.items.some((selected) => selected.id === item.id)) {
                            this.items.push(item);
                        }

                        this.syncIds();
                        this.query = '';
                        this.results = [];
                    },
                    remove(id) {
                        this.items = this.items.filter((item) => item.id !== id);
                        this.syncIds();
                    },
                    search() {
                        if (this.query.trim().length < 1) {
                            this.results = [];
                            this.loading = false;
                            return;
                        }

                        this.loading = true;
                        fetch(this.endpoint + '?q=' + encodeURIComponent(this.query))
                            .then((response) => response.json())
                            .then((results) => this.results = results.filter((result) => ! this.items.some((item) => item.id === result.id)))
                            .catch(() => this.results = [])
                            .finally(() => this.loading = false);
                    },
                    syncIds() {
                        this.idsText = this.items.map((item) => item.id).join(',');
                    },
                };
            };
        </script>
    </x-slot:js>

    <div id="filter">
        <form method="post" action="{{ route('board.setfilter') }}">
            @csrf
            <input type="text" name="title" placeholder="Thementitel &hellip;" value="{{ $filters['title'] }}">
            <input type="text" name="message" placeholder="Beitrag &hellip;" value="{{ $filters['message'] }}">

            @include('board._board-filter-tree', ['boards' => $boards, 'selectedBoards' => $filters['board'], 'showSettings' => $showSettings])

            @include('board._person-filter', [
                'endpoint' => route('board.ajax_get_users'),
                'label' => 'Nutzer hat Thema eröffnet ...',
                'lookup' => $filterLookups['users'],
                'name' => 'user_first',
                'searchPlaceholder' => 'Nutzer suchen ...',
                'selectedIds' => $filters['user_first'],
            ])

            @include('board._person-filter', [
                'endpoint' => route('board.ajax_get_users'),
                'label' => 'Nutzer hat im Thema geschrieben ...',
                'lookup' => $filterLookups['users'],
                'name' => 'user_contains',
                'searchPlaceholder' => 'Nutzer suchen ...',
                'selectedIds' => $filters['user_contains'],
            ])

            @include('board._person-filter', [
                'endpoint' => route('board.ajax_get_users'),
                'label' => 'Nutzer hat letzten Beitrag geschrieben ...',
                'lookup' => $filterLookups['users'],
                'name' => 'user_last',
                'searchPlaceholder' => 'Nutzer suchen ...',
                'selectedIds' => $filters['user_last'],
            ])

            @include('board._person-filter', [
                'endpoint' => route('board.ajax_get_chars'),
                'label' => 'Charakter hat Thema eröffnet ...',
                'lookup' => $filterLookups['characters'],
                'name' => 'char_first',
                'searchPlaceholder' => 'Charakter suchen ...',
                'selectedIds' => $filters['char_first'],
            ])

            @include('board._person-filter', [
                'endpoint' => route('board.ajax_get_chars'),
                'label' => 'Charakter hat im Thema geschrieben ...',
                'lookup' => $filterLookups['characters'],
                'name' => 'char_contains',
                'searchPlaceholder' => 'Charakter suchen ...',
                'selectedIds' => $filters['char_contains'],
            ])

            @include('board._person-filter', [
                'endpoint' => route('board.ajax_get_chars'),
                'label' => 'Charakter hat letzten Beitrag geschrieben ...',
                'lookup' => $filterLookups['characters'],
                'name' => 'char_last',
                'searchPlaceholder' => 'Charakter suchen ...',
                'selectedIds' => $filters['char_last'],
            ])

            <label for="date_first">Erstellt am oder seit ...</label>
            <input type="text" id="date_first" name="date_first" placeholder="7d oder 01.01.2026-31.01.2026" value="{{ $filters['date_first'] }}">

            <label for="date_last">Letzter Beitrag am oder seit ...</label>
            <input type="text" id="date_last" name="date_last" placeholder="7d oder 01.01.2026-31.01.2026" value="{{ $filters['date_last'] }}">

            <input type="submit" name="submit" value="Filtern">
        </form>
    </div>

    <div id="threads">
        @if ($canCreateThread)
        <p class="newthread"><a href="{{ route('thread.create') }}" class="fa-comments">neues Thema</a></p>
        @endif

        @if ($threads->isNotEmpty())
        <p>{{ number_format($threads->total(), 0, ',', '.') }} {{ $threads->total() === 1 ? 'Ergebnis' : 'Ergebnisse' }}</p>
        @include('board._pagination', ['paginator' => $threads, 'baseUrl' => $filter ? url('/board/filter/'.$filter) : url('/board/filter')])
        @include('board._thread-list', ['threads' => $threads, 'viewedThreads' => $viewedThreads ?? []])
        @include('board._pagination', ['paginator' => $threads, 'baseUrl' => $filter ? url('/board/filter/'.$filter) : url('/board/filter')])
        @else
        <p>Keine Themen gefunden!</p>
        @endif
    </div>
</x-main-layout>
