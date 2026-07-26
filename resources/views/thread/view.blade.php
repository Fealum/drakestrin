@inject('forumFormatter', 'App\Services\Board\ForumFormatter')

<x-main-layout :title="$thread->name" css="thread">
    <x-slot:js>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const postForm = document.querySelector('form[name="newpost"]');

                if (postForm) {
                    const transferAction = postForm.querySelector('[name="transfer_action"]');
                    const transferRecipient = document.getElementById('transfer-recipient');
                    const locationInventory = document.getElementById('inventory-location');
                    const companyField = document.getElementById('transfer-company');
                    const companySelect = companyField?.querySelector('select');

                    const setInventoryEnabled = function (list, enabled) {
                        if (! list) {
                            return;
                        }

                        list.style.display = enabled ? '' : 'none';
                        list.querySelectorAll('input').forEach((input) => input.disabled = ! enabled);
                    };

                    const updateInventoryVisibility = function () {
                        postForm.querySelectorAll('ul.inventory-char').forEach((list) => {
                            setInventoryEnabled(list, false);
                        });
                        postForm.querySelectorAll('ul.inventory-company').forEach((list) => {
                            setInventoryEnabled(list, false);
                        });

                        setInventoryEnabled(locationInventory, transferAction?.value === 'pickup');

                        const selectedCharacter = postForm.querySelector('input[name="character"]:checked');

                        if (selectedCharacter && ['give', 'drop', 'company_deposit'].includes(transferAction?.value)) {
                            const list = document.getElementById('inventory-char-' + selectedCharacter.value);
                            setInventoryEnabled(list, true);
                        }

                        if (companyField) {
                            const usesCompany = ['company_deposit', 'company_withdrawal'].includes(transferAction?.value);
                            companyField.style.display = usesCompany ? '' : 'none';
                            companyField.querySelectorAll('input, select').forEach((input) => input.disabled = ! usesCompany);

                            if (usesCompany && companySelect) {
                                companySelect.querySelectorAll('option').forEach((option) => {
                                    const representatives = (option.dataset.representatives || '').split(',');
                                    option.disabled = transferAction.value === 'company_withdrawal'
                                        && (! selectedCharacter || ! representatives.includes(selectedCharacter.value));
                                });

                                if (companySelect.selectedOptions[0]?.disabled) {
                                    const available = Array.from(companySelect.options).find((option) => ! option.disabled);
                                    companySelect.value = available?.value || '';
                                }

                                if (transferAction.value === 'company_withdrawal' && companySelect.value) {
                                    setInventoryEnabled(document.getElementById('inventory-company-' + companySelect.value), true);
                                }
                            }
                        }

                        if (transferRecipient) {
                            const enabled = ['give', 'company_withdrawal'].includes(transferAction?.value);
                            transferRecipient.style.display = enabled ? '' : 'none';
                            transferRecipient.querySelectorAll('input, select, button').forEach((input) => input.disabled = ! enabled);
                        }
                    };

                    postForm.querySelectorAll('input[name="character"]').forEach((input) => {
                        input.addEventListener('change', updateInventoryVisibility);
                    });
                    transferAction?.addEventListener('change', updateInventoryVisibility);
                    companySelect?.addEventListener('change', updateInventoryVisibility);

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
                <a class="option delete" title="{{ $post->transfers->isNotEmpty() ? 'Inhalt löschen' : 'löschen' }}" href="{{ route('post.delete', ['post' => $post->id]) }}">{{ $post->transfers->isNotEmpty() ? 'Inhalt löschen' : 'löschen' }}</a>
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
                    @if ($transfer->reversal_of_transfer_id)
                    <strong>Rückabwicklung:</strong>
                    @endif
                    @php($sender = $transfer->sender)
                    @php($recipient = $transfer->recipient)
                    @include('transfer._participant', ['participant' => $sender])
                    &rarr;
                    @foreach ($transfer->items as $transferItem)
                        @php($item = $transferItem->item)
                        @if ($item)
                        {{ $item->name }} ({{ $item->makeunitary($transferItem->stack) }})
                        @endif
                    @endforeach
                    &rarr;
                    @include('transfer._participant', ['participant' => $recipient])

                    @if ($transfer->reversal)
                    <small>rückgängig gemacht</small>
                    @endif

                    @if ($reversibleTransferIds->contains($transfer->id))
                    <form action="{{ route('transfer.reverse', ['transfer' => $transfer->id]) }}" method="post" class="transfer-reversal-form">
                        @csrf
                        <button type="submit" onclick="return confirm('Diese Handlung wirklich rückgängig machen?')">rückgängig machen</button>
                    </form>
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
    @php($inventoryCompanies = $representedLocalCompanies->filter(fn ($company) => $company->inventory->isNotEmpty())->values())
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

            @if ($canTransfer && $thread->currentScene?->story_started_at !== null && ($inventoryCharacters->isNotEmpty() || $locationInventory->isNotEmpty() || $localCompanies->isNotEmpty()))
            <p>
                <label for="transfer_action">Handlung</label>
                <select name="transfer_action" id="transfer_action">
                    <option value="">Keine</option>
                    @if ($inventoryCharacters->isNotEmpty())
                    <option value="give" @selected(old('transfer_action') === 'give')>An Charakter geben</option>
                    <option value="drop" @selected(old('transfer_action') === 'drop')>Am Ort ablegen</option>
                    @endif
                    @if ($locationInventory->isNotEmpty())
                    <option value="pickup" @selected(old('transfer_action') === 'pickup')>Vom Ort aufnehmen</option>
                    @endif
                    @if ($inventoryCharacters->isNotEmpty() && $localCompanies->isNotEmpty())
                    <option value="company_deposit" @selected(old('transfer_action') === 'company_deposit')>An örtlichen Betrieb geben</option>
                    @endif
                    @if ($inventoryCompanies->isNotEmpty())
                    <option value="company_withdrawal" @selected(old('transfer_action') === 'company_withdrawal')>Für örtlichen Betrieb aushändigen</option>
                    @endif
                </select>
            </p>

            @if ($localCompanies->isNotEmpty())
            <p id="transfer-company">
                <label for="company">Betrieb</label>
                <select name="company" id="company">
                    @foreach ($localCompanies as $company)
                    @php($representativeIds = $company->representatives->pluck('character_id')->push($company->character_id)->unique()->implode(','))
                    <option
                        value="{{ $company->id }}"
                        data-representatives="{{ $representativeIds }}"
                        @selected((int) old('company') === (int) $company->id)
                    >{{ $company->name }}</option>
                    @endforeach
                </select>
            </p>
            @endif

            @foreach ($inventoryCharacters as $character)
            <ul class="inventory-char" id="inventory-char-{{ $character->id }}">
                @foreach ($character->inventory as $inventory)
                @php($item = $inventory->item)
                <li>
                    <input name="inventory[{{ $inventory->id }}]" value="{{ $inventory->id }}" id="inventory-{{ $inventory->id }}" type="checkbox" @checked(array_key_exists($inventory->id, (array) old('inventory', [])))>
                    <label for="inventory-{{ $inventory->id }}">
                        @if ($item)
                        <img src="{{ url('/img/item.img/'.$item->img.'.png') }}" title="{{ $item->name }}" alt="{{ $item->name }}">
                        <span>{{ $item->name }}</span>
                        @endif
                        @if ($item && $item->stackable && $inventory->stack > 1)
                        <input name="inventorystack[{{ $inventory->id }}]" value="{{ old('inventorystack.'.$inventory->id, $inventory->makeunitary()) }}" type="text">
                        @endif
                    </label>
                </li>
                @endforeach
            </ul>
            @endforeach

            @if ($locationInventory->isNotEmpty())
            <ul class="inventory-location" id="inventory-location">
                @foreach ($locationInventory as $inventory)
                @php($item = $inventory->item)
                <li>
                    <input name="inventory[{{ $inventory->id }}]" value="{{ $inventory->id }}" id="inventory-{{ $inventory->id }}" type="checkbox" @checked(array_key_exists($inventory->id, (array) old('inventory', [])))>
                    <label for="inventory-{{ $inventory->id }}">
                        @if ($item)
                        <img src="{{ url('/img/item.img/'.$item->img.'.png') }}" title="{{ $item->name }}" alt="{{ $item->name }}">
                        <span>{{ $item->name }}</span>
                        @endif
                        @if ($item && $item->stackable && $inventory->stack > 1)
                        <input name="inventorystack[{{ $inventory->id }}]" value="{{ old('inventorystack.'.$inventory->id, $inventory->makeunitary()) }}" type="text">
                        @endif
                    </label>
                </li>
                @endforeach
            </ul>
            @endif

            @foreach ($inventoryCompanies as $company)
            <ul class="inventory-company" id="inventory-company-{{ $company->id }}">
                @foreach ($company->inventory as $inventory)
                @php($item = $inventory->item)
                <li>
                    <input name="inventory[{{ $inventory->id }}]" value="{{ $inventory->id }}" id="inventory-{{ $inventory->id }}" type="checkbox" @checked(array_key_exists($inventory->id, (array) old('inventory', [])))>
                    <label for="inventory-{{ $inventory->id }}">
                        @if ($item)
                        <img src="{{ url('/img/item.img/'.$item->img.'.png') }}" title="{{ $item->name }}" alt="{{ $item->name }}">
                        <span>{{ $item->name }}</span>
                        @endif
                        @if ($item && $item->stackable && $inventory->stack > 1)
                        <input name="inventorystack[{{ $inventory->id }}]" value="{{ old('inventorystack.'.$inventory->id, $inventory->makeunitary()) }}" type="text">
                        @endif
                    </label>
                </li>
                @endforeach
            </ul>
            @endforeach

            <div id="transfer-recipient">
            <x-character-selector
                name="recipient"
                :endpoint="route('board.ajax_get_chars')"
                label="an"
                placeholder="Empfänger suchen ..."
            />
            </div>
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
