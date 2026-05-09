<x-main-layout title="Übersetzung verknüpfen" css="dictionary">
    <div
        class="dictionary-key-search"
        x-data="dictionaryKeySearch(@js(route('dictionary.create_key', ['word' => $word->id])))"
    >
        <form name="searchdictionarykey" action="{{ route('dictionary.create_key', ['word' => $word->id]) }}" method="get" x-on:submit="submitSearch">
            <p>
                <label for="q">Wort suchen: </label>
                <input
                    type="search"
                    name="q"
                    id="q"
                    value="{{ $query }}"
                    x-ref="query"
                    x-on:input.debounce.300ms="search"
                    autocomplete="off"
                    autofocus
                />
                <input class="js-hidden" type="submit" value="Wort suchen" />
            </p>
        </form>

        <div x-ref="results">
            @include('dictionary._key-search-results', [
                'query' => $query,
                'searchResults' => $searchResults,
                'word' => $word,
            ])
        </div>
    </div>

    @push('scripts')
    <style>
        .js .js-hidden {
            display: none !important;
        }
    </style>
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
    <script>
    document.addEventListener('alpine:init', function () {
        Alpine.data('dictionaryKeySearch', function (searchUrl) {
            return {
                loading: false,

                submitSearch: function (event) {
                    event.preventDefault();
                    this.search();
                },

                search: function () {
                    const query = this.$refs.query.value.trim();

                    if (query.length < 2) {
                        this.$refs.results.innerHTML = '';
                        return;
                    }

                    this.loading = true;

                    fetch(searchUrl + '?q=' + encodeURIComponent(query), {
                        headers: {
                            'X-Dictionary-Search': 'partial',
                        },
                    })
                        .then(function (response) { return response.text(); })
                        .then((html) => {
                            this.$refs.results.innerHTML = html;
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
            };
        });
    });
    </script>
    @endpush
</x-main-layout>
