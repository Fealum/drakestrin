@inject('forumFormatter', 'App\Services\Board\ForumFormatter')

<x-main-layout :title="$thread->name" css="thread">
    <x-slot:js>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const postForm = document.querySelector('form[name="newpost"]');

                if (postForm) {
                    const updateInventoryVisibility = function () {
                        postForm.querySelectorAll('ul.inventory-char').forEach((list) => {
                            list.style.display = 'none';
                        });

                        const selectedCharacter = postForm.querySelector('input[name="character"]:checked');

                        if (selectedCharacter) {
                            const list = document.getElementById('inventory-char-' + selectedCharacter.value);

                            if (list) {
                                list.style.display = '';
                            }
                        }
                    };

                    postForm.querySelectorAll('input[name="character"]').forEach((input) => {
                        input.addEventListener('change', updateInventoryVisibility);
                    });

                    updateInventoryVisibility();
                }
            });

            window.insertThreadQuote = function (quote) {
                const textarea = document.getElementById('newpost-message');

                if (! textarea) {
                    return;
                }

                const start = typeof textarea.selectionStart === 'number' ? textarea.selectionStart : textarea.value.length;
                const end = typeof textarea.selectionEnd === 'number' ? textarea.selectionEnd : start;
                const before = textarea.value.slice(0, start);
                const after = textarea.value.slice(end);
                const prefix = before.length > 0 && ! before.endsWith('\n') ? '\n' : '';
                const suffix = after.length > 0 && ! quote.endsWith('\n') ? '\n' : '';
                const insertion = prefix + quote + suffix;

                textarea.value = before + insertion + after;
                textarea.focus();
                textarea.selectionStart = textarea.selectionEnd = start + insertion.length;
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
            };

            window.singleCharacterSelector = function ({ endpoint, name }) {
                return {
                    endpoint,
                    enhanced: false,
                    idText: '',
                    loading: false,
                    name,
                    query: '',
                    results: [],
                    selected: null,
                    init() {
                        this.enhanced = true;
                    },
                    clear() {
                        this.selected = null;
                        this.idText = '';
                        this.query = '';
                        this.results = [];
                    },
                    select(character) {
                        this.selected = character;
                        this.idText = character.id;
                        this.query = '';
                        this.results = [];
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
                            .then((results) => this.results = results)
                            .catch(() => this.results = [])
                            .finally(() => this.loading = false);
                    },
                };
            };
        </script>
    </x-slot:js>

    <p>
        {{ number_format($thread->views, 0, ',', '.') }} Aufrufe,
        {{ number_format($thread->post_count, 0, ',', '.') }} Beiträge.
        @if ($canEditThread)
        <a class="option edit" title="editieren" href="{{ route('thread.edit', ['thread' => $thread->id]) }}">editieren</a>
        @endif
        @if ($canDeleteThread)
        <a class="option delete" title="löschen" href="{{ route('thread.delete', ['thread' => $thread->id]) }}">löschen</a>
        @endif
        @if ($canSetScene)
        <a class="option scene" title="Szene setzen" href="{{ route('thread.scene.create', ['thread' => $thread->id]) }}">Szene setzen</a>
        @endif
        @if ($thread->currentScene && $canEndScene)
        <a class="option scene" title="Szene beenden" href="{{ route('thread.scene.end', ['thread' => $thread->id]) }}">Szene beenden</a>
        @endif
    </p>

    @include('board._pagination', ['paginator' => $posts, 'baseUrl' => url('/thread/view/'.$thread->id)])

    @forelse ($timelineEntries as $entry)
    @if ($entry['type'] === 'scene_start')
    @php($scene = $entry['scene'])
    <div class="thread-scene" id="scene{{ $scene->id }}">
        <p>
            Szene:
            <a href="{{ route('location.view', ['location' => $scene->location_id]) }}">{{ $scene->location?->name ?? 'Unbekannter Ort' }}</a>
            @if ($scene->story_started_at)
            ab <x-datetime :time="\Illuminate\Support\Carbon::createFromTimestamp($scene->story_started_at)" />
            @endif
        </p>
    </div>
    @elseif ($entry['type'] === 'scene_end')
    @php($scene = $entry['scene'])
    <div class="thread-scene">
        <p>
            Szene beendet:
            <a href="{{ route('location.view', ['location' => $scene->location_id]) }}">{{ $scene->location?->name ?? 'Unbekannter Ort' }}</a>
            @if ($scene->story_ended_at)
            um <x-datetime :time="\Illuminate\Support\Carbon::createFromTimestamp($scene->story_ended_at)" />
            @endif
        </p>
    </div>
    @else
    @php($post = $entry['post'])
    @php($character = $post->character)
    <div id="post{{ $post->id }}" class="post">
        @if ($character)
        <x-avatar :subject="$character" size="post" />
        @endif

        <div class="postuser">
            <h4>
                @if (auth()->check() && $post->time?->timestamp >= auth()->user()->lastvisit?->timestamp && (($viewedThreads[$thread->id] ?? 0) < $post->getRawOriginal('time')))
                <span class="option new">(Neu)</span>
                @endif
                @if ($character)
                <a href="{{ url('/user/character/'.$character->id) }}">{{ $character->name }}</a>
                @else
                Unbekannter Charakter
                @endif
                <span class="datetime"><x-datetime :time="$post->time" /></span>
            </h4>
            <p>
                <a class="postnumber small" href="{{ url('/thread/view/'.$thread->id.($posts->currentPage() > 1 ? '/'.$posts->currentPage() : '')) }}#post{{ $post->id }}">{{ $entry['post_number'] + (($posts->currentPage() - 1) * $posts->perPage()) }}</a>
                <a
                    class="option quote"
                    title="zitieren"
                    href="{{ route('thread.view', ['thread' => $thread->id, 'page' => 'last', 'quote' => $post->id]) }}#newpost"
                    @if ($canCreatePost && ($characters->isNotEmpty() || $canCreateCharacter))
                    x-data
                    x-on:click.prevent="window.insertThreadQuote(@js('[q='.str_replace(']', ')', $character?->name ?? $post->author?->name ?? 'Unbekannter Charakter').']'.trim($post->message).'[/q]'."\n"))"
                    @endif
                >zitieren</a>
                @can('update', $post)
                <a class="option edit" title="editieren" href="{{ route('post.edit', ['post' => $post->id]) }}">editieren</a>
                @endcan
                @can('delete', $post)
                <a class="option delete" title="löschen" href="{{ route('post.delete', ['post' => $post->id]) }}">löschen</a>
                @endcan
                <a class="option report" title="melden" href="#">melden</a>
                @can('viewIp', $post)
                <a class="option ip" title="IP" href="{{ route('post.ip', ['post' => $post->id]) }}">IP</a>
                @endcan
            </p>
        </div>

        <div class="postcontent">
            @if (trim($post->message) !== '')
                {!! $forumFormatter->render($post->message, $post->smilies) !!}
            @endif

            @if ($post->transfers->isNotEmpty())
                @foreach ($post->transfers as $transfer)
                <div class="post-transfer">
                    @php($sender = $transfer->sender)
                    @php($recipient = $transfer->recipient)
                    @if ($sender)
                    <a href="{{ url('/user/character/'.$sender->id) }}"><x-avatar :subject="$sender" size="list" :title="$sender->name" /></a>
                    @endif
                    &rarr;
                    @foreach ($transfer->items as $transferItem)
                        @php($item = $transferItem->item)
                        @if ($item)
                        {{ $item->name }} ({{ $item->makeunitary($transferItem->stack) }})
                        @endif
                    @endforeach
                    &rarr;
                    @if ($recipient)
                    <a href="{{ url('/user/character/'.$recipient->id) }}"><x-avatar :subject="$recipient" size="list" :title="$recipient->name" /></a>
                    @endif
                </div>
                @endforeach
            @endif
        </div>
    </div>
    @endif
    @empty
    <p>Keine Beiträge!</p>
    @endforelse

    @include('board._pagination', ['paginator' => $posts, 'baseUrl' => url('/thread/view/'.$thread->id)])

    @if ($canCreatePost)
    @php($inventoryCharacters = $characters->filter(fn ($character) => $character->inventory->isNotEmpty())->values())

    @if ($characters->isNotEmpty() || $canCreateCharacter)
    <div id="newpost" class="post">
        <form name="newpost" action="{{ route('post.create', ['thread' => $thread->id]) }}" method="post">
            @csrf
            <div class="post-charselect">
                <ul>
                    @foreach ($characters as $character)
                    <li>
                        <input name="character" value="{{ $character->id }}" id="char-{{ $character->id }}" type="radio" @checked(old('character', $characters->first()->id) == $character->id)>
                        <label for="char-{{ $character->id }}">
                            <x-avatar :subject="$character" :title="$character->name" />
                        </label>
                    </li>
                    @endforeach
                    @if ($canCreateCharacter)
                    <li>
                        <input name="character" value="new" id="char-new" type="radio" @checked(old('character') === 'new' || $characters->isEmpty())>
                        <label for="char-new">
                            <img src="{{ asset('css/img/newchar.png') }}" title="Neuer Charakter" alt="Neuer Charakter">
                            <input type="text" name="newcharname" maxlength="85" placeholder="Neuer Charakter" value="{{ old('newcharname') }}">
                        </label>
                    </li>
                    @endif
                </ul>
            </div>
            <input type="hidden" name="smilies" value="1">
            <input type="hidden" name="signature" value="1">
            <x-bbcode-textarea name="message" id="newpost-message" :value="old('message', $quotedMessage)" />

            @if ($canTransfer && $inventoryCharacters->isNotEmpty() && $thread->currentScene)
            @foreach ($inventoryCharacters as $character)
            <ul class="inventory-char" id="inventory-char-{{ $character->id }}">
                @foreach ($character->inventory as $inventory)
                @php($item = $inventory->item)
                <li>
                    <input name="inventory[{{ $inventory->id }}]" value="{{ $inventory->id }}" id="inventory-{{ $inventory->id }}" type="checkbox">
                    <label for="inventory-{{ $inventory->id }}">
                        @if ($item)
                        <img src="{{ url('/img/item.img/'.$item->img.'.png') }}" title="{{ $item->name }}" alt="{{ $item->name }}">
                        @endif
                        @if ($item && $item->stackable && $inventory->stack > 1)
                        <input name="inventorystack[{{ $inventory->id }}]" value="{{ $inventory->makeunitary() }}" type="text">
                        @endif
                    </label>
                </li>
                @endforeach
            </ul>
            @endforeach

            <x-character-selector
                name="recipient"
                :endpoint="route('board.ajax_get_chars')"
                label="an"
                placeholder="Empfänger suchen ..."
            />
            @endif

            <input type="submit" value="Neuen Beitrag erstellen">
        </form>
    </div>
    @else
    <div class="nochar">
        <h3>Noch kein Charakter vorhanden!</h3>
        <p>Um Beiträge zu verfassen, musst Du zuerst einen Charakter erstellen.</p>
    </div>
    @endif
    @endif
</x-main-layout>
