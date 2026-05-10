@inject('forumFormatter', 'App\Services\Board\ForumFormatter')

<x-main-layout :title="$thread->name" css="thread">
    <x-slot:js>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const actionForm = document.querySelector('form[name="newtransfer"]');

                const actionTabs = document.getElementById('selectaction');
                const newPost = document.getElementById('newpost');
                const newAction = document.getElementById('newaction');

                if (actionTabs && newPost && newAction) {
                    const showActionPanel = function (target) {
                        newPost.style.display = target === 'newpost' ? '' : 'none';
                        newAction.style.display = target === 'newaction' ? '' : 'none';

                        actionTabs.querySelectorAll('a[href="#newpost"], a[href="#newaction"]').forEach((link) => {
                            link.classList.toggle('activeaction', link.getAttribute('href') === '#' + target);
                        });
                    };

                    actionTabs.querySelectorAll('a[href="#newpost"], a[href="#newaction"]').forEach((link) => {
                        link.addEventListener('click', function (event) {
                            event.preventDefault();
                            showActionPanel(link.hash.slice(1));
                        });
                    });

                    showActionPanel(window.location.hash === '#newaction' ? 'newaction' : 'newpost');
                }

                if (actionForm) {
                    const updateInventoryVisibility = function () {
                        actionForm.querySelectorAll('ul.inventory-char').forEach((list) => {
                            list.style.display = 'none';
                        });

                        const selectedCharacter = actionForm.querySelector('input[name="character"]:checked');

                        if (selectedCharacter) {
                            const list = document.getElementById('inventory-char-' + selectedCharacter.value);

                            if (list) {
                                list.style.display = '';
                            }
                        }
                    };

                    actionForm.querySelectorAll('input[name="character"]').forEach((input) => {
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
        {{ number_format($thread->post__total, 0, ',', '.') }} Beiträge.
        @if ($canEditThread)
        <a class="option edit" title="editieren" href="{{ route('thread.edit', ['thread' => $thread->id]) }}">editieren</a>
        @endif
        @if ($canDeleteThread)
        <a class="option delete" title="löschen" href="{{ route('thread.delete', ['thread' => $thread->id]) }}">löschen</a>
        @endif
    </p>

    @include('board._pagination', ['paginator' => $posts, 'baseUrl' => url('/thread/view/'.$thread->id)])

    @forelse ($posts as $post)
    @php($character = $post->characterModel)
    <div id="post{{ $post->id }}" class="post">
        @if ($character)
        <img src="{{ url('/img/character_avatar.id/thumb/'.$character->avatarThumbPath().'.jpg') }}" alt="">
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
                <a class="postnumber small" href="{{ url('/thread/view/'.$thread->id.($posts->currentPage() > 1 ? '/'.$posts->currentPage() : '')) }}#post{{ $post->id }}">{{ $loop->iteration + (($posts->currentPage() - 1) * $posts->perPage()) }}</a>
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
            @if ($post->transfers->isNotEmpty())
                @foreach ($post->transfers as $transfer)
                    @php($sender = $transfer->senderCharacter)
                    @php($recipient = $transfer->recipientCharacter)
                    @if ($sender)
                    <a href="{{ url('/user/character/'.$sender->id) }}"><img src="{{ url('/img/character_avatar.id/thumb/'.$sender->avatarThumbPath().'.jpg') }}" title="{{ $sender->name }}" alt="{{ $sender->name }}"></a>
                    @endif
                    &rarr;
                    @foreach ($transfer->items as $transferItem)
                        @php($item = $transferItem->itemModel)
                        @if ($item)
                        {{ $item->name }} ({{ $item->makeunitary($transferItem->stack) }})
                        @endif
                    @endforeach
                    &rarr;
                    @if ($recipient)
                    <a href="{{ url('/user/character/'.$recipient->id) }}"><img src="{{ url('/img/character_avatar.id/thumb/'.$recipient->avatarThumbPath().'.jpg') }}" title="{{ $recipient->name }}" alt="{{ $recipient->name }}"></a>
                    @endif
                @endforeach
            @else
                {!! $forumFormatter->render($post->message, $post->smilies) !!}
            @endif
        </div>
    </div>
    @empty
    <p>Keine Beiträge!</p>
    @endforelse

    @include('board._pagination', ['paginator' => $posts, 'baseUrl' => url('/thread/view/'.$thread->id)])

    @if ($canCreatePost)
    @php($inventoryCharacters = $characters->filter(fn ($character) => $character->inventory->isNotEmpty())->values())
    @if ($canTransfer && $inventoryCharacters->isNotEmpty())
    <ul id="selectaction">
        <li><a class="activeaction" href="#newpost">Beitrag</a></li>
        <li><a href="#newaction">Handlung</a></li>
    </ul>
    @endif

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
                            <img src="{{ url('/img/character_avatar.id/thumb/'.$character->avatarThumbPath().'.jpg') }}" title="{{ $character->name }}" alt="{{ $character->name }}">
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
            <input type="submit" value="Neuen Beitrag erstellen">
        </form>
    </div>
    @else
    <div class="nochar">
        <h3>Noch kein Charakter vorhanden!</h3>
        <p>Um Beiträge zu verfassen, musst Du zuerst einen Charakter erstellen.</p>
    </div>
    @endif

    @if ($canTransfer && $inventoryCharacters->isNotEmpty())
    <div id="newaction" class="post">
        <form name="newtransfer" action="{{ route('transfer.transfer', ['thread' => $thread->id]) }}" method="post">
            @csrf
            <div class="post-charselect">
                <ul>
                    @foreach ($inventoryCharacters as $character)
                    <li>
                        <input name="character" value="{{ $character->id }}" id="action-char-{{ $character->id }}" type="radio" @checked($loop->first)>
                        <label for="action-char-{{ $character->id }}">
                            <img src="{{ url('/img/character_avatar.id/thumb/'.$character->avatarThumbPath().'.jpg') }}" title="{{ $character->name }}" alt="{{ $character->name }}">
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>

            @foreach ($inventoryCharacters as $character)
            <h5>{{ $character->name }}</h5>
            <ul class="inventory-char" id="inventory-char-{{ $character->id }}">
                @foreach ($character->inventory as $inventory)
                @php($item = $inventory->itemModel)
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
            <input type="submit" value="Handlung ausführen">
        </form>
    </div>
    @endif
    @endif
</x-main-layout>
