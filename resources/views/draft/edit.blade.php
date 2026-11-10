@php($selectedCharacter = old('character', $draft->characterSelection() ?? ($characters->isEmpty() && $canCreateCharacter ? 'new' : null)))
@php($selectedCharacterId = is_numeric($selectedCharacter) ? (int) $selectedCharacter : null)
@php($selectedCharacterModel = $characters->firstWhere('id', $selectedCharacterId))
@php($authorName = $selectedCharacter === 'new' ? (old('newcharname', $draft->new_character_name) ?: 'Neuer Charakter') : ($selectedCharacterModel?->name ?? 'Charakter auswählen'))
<div id="newpost" class="post" x-data="postComposer">
    <div id="composer-feedback">
        @if(!$thread || request()->header('HX-Request') === 'true')<x-validation-errors />@endif
    </div>
    <span class="composer-request-status" role="status" aria-live="polite" x-text="busy ? 'Wird verarbeitet ...' : ''"></span>
    <form method="post" action="{{ $thread ? route('draft.reply.update', $thread) : ($draft->exists ? route('draft.update', $draft) : route('draft.topic.update', $draft->board ?: [])) }}" class="post-composer" id="post-composer" x-ref="form" x-on:input="changed" x-on:change="contextChanged" x-on:focusin="rememberSelection" x-on:focusout="leftBlock" x-on:select="rememberSelection" x-on:keyup="rememberSelection" x-on:mouseup="rememberSelection" x-on:submit.prevent="submit" x-on:composer-response="response" x-bind:aria-busy="busy || saving">
        @csrf
        <img class="composer-author-portrait" src="{{ $selectedCharacterModel?->avatarThumbUrl() ?? asset('css/img/newchar.png') }}" alt="{{ $authorName }}">
        <div class="composer-main">
        <button type="submit" name="intent" value="save" formnovalidate hidden>Entwurf speichern</button>
        <input type="hidden" name="version" value="{{ $draft->version }}" id="draft-version">
        <input type="hidden" name="element_index" value="" id="element-index">
        <input type="hidden" name="option_index" value="" id="option-index">
        <input type="hidden" name="target_index" value="" id="target-index">
        @if($thread)
        <input type="hidden" name="thread_page" value="{{ $threadPage ?? 1 }}">
        @endif

        @unless($thread)
        <p><label for="draft-title">Titel</label><input id="draft-title" name="title" maxlength="225" required value="{{ old('title', $draft->title) }}"></p>
        <p><label for="draft-board">Forum</label><select id="draft-board" name="board" required>
            <option value="">Forum wählen</option>
            @foreach($boards as $board)<option value="{{ $board->id }}" @selected((int) old('board', $draft->board_id) === $board->id)>{{ $board->name }}</option>@endforeach
        </select></p>
        @endunless

        <header class="composer-author">
                <h4 class="composer-author-name">{{ $authorName }}</h4>
                <div class="composer-new-character">
                    <label for="composer-new-character-name" class="sr-only">Name</label>
                    <input id="composer-new-character-name" type="text" name="newcharname" maxlength="85" placeholder="Neuer Charakter" value="{{ old('newcharname', $draft->new_character_name) }}" autocomplete="off">
                </div>
                <fieldset class="composer-character-picker">
                    <legend class="sr-only">Charakter auswählen</legend>
                    <ul>
                @foreach($characters as $character)
                <li>
                    <input type="radio" name="character" value="{{ $character->id }}" id="char-{{ $character->id }}" data-name="{{ $character->name }}" data-avatar="{{ $character->avatarThumbUrl() }}" @checked($selectedCharacterId === $character->id) required />
                    <label for="char-{{ $character->id }}">
                        <x-avatar :subject="$character" size="list" :title="$character->name" />
                    </label>
                </li>
                @endforeach
                @if ($canCreateCharacter)
                <li>
                    <input type="radio" name="character" value="new" id="char-new" data-name="Neuer Charakter" data-avatar="{{ asset('css/img/newchar.png') }}" @checked($selectedCharacter === 'new' || $characters->isEmpty()) required />
                    <label for="char-new" class="composer-character-create" title="Neuer Charakter">
                        <img src="{{ asset('css/img/newchar.png') }}" title="Neuer Charakter" alt="Neuer Charakter">
                    </label>
                </li>
                @endif
                    </ul>
                </fieldset>
                <noscript><button type="submit" formnovalidate name="intent" value="save">Charakterauswahl übernehmen</button></noscript>
        </header>

        @if($initialScene)
        <aside class="composer-scene-context">
            <strong>Aktive Szene:</strong>
            @if($initialScene->location)
            <a href="{{ route('location.view', $initialScene->location) }}">{{ $initialScene->location?->name ?? 'Unbekannter Ort' }}</a>
            @else
            Unbekannter Ort
            @endif
            @if($initialScene->story_started_at !== null)
            · <x-datetime :time="\Illuminate\Support\Carbon::createFromTimestamp($initialScene->story_started_at)" />
            @else
            · ohne gesetzte Spielzeit
            @endif
        </aside>
        @endif

        @php($composerElements = $draft->payload)
        <div class="composer-elements" id="composer-elements">
            <template x-if="true"><x-bbcode-toolbar :composer="true" :can-transfer="$canTransfer" :can-create-poll="$canCreatePoll" :can-set-scene="$canSetScene" :can-end-scene="$canEndScene" /></template>
            @foreach($composerElements as $index => $element)
                @include('draft._element', ['element' => $element, 'index' => $index, 'elementCount' => count($composerElements)])
            @endforeach
        </div>
        <footer class="composer-submit">
            <button type="submit" formnovalidate name="intent" value="save" class="composer-save">speichern</button>
            <button type="submit" formnovalidate name="intent" value="publish" class="composer-publish">veröffentlichen</button>
            <span id="draft-save-status" role="status" aria-live="polite" x-ref="status" x-text="status">
                @if($draft->exists)Gespeichert <x-datetime :time="$draft->updated_at" />@else Noch nicht gespeichert @endif
            </span>
            <a x-cloak x-show="conflict" x-ref="resume" x-bind:href="resumeUrl" href="{{ $thread ? route('thread.view', ['thread' => $thread, 'page' => ($threadPage ?? 1) > 1 ? $threadPage : null]).'#post-composer' : ($draft->exists ? route('draft.edit', $draft) : route('draft.topic', $draft->board ?: [])) }}">Neueren Entwurf öffnen</a>
        </footer>
        </div>
    </form>
</div>
