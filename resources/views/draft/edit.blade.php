    <form method="post" action="{{ $thread ? route('draft.reply.update', $thread) : ($draft->exists ? route('draft.update', $draft) : route('draft.topic.update', $draft->board ?: [])) }}" class="post-composer" id="post-composer">
        @csrf
        <button type="submit" name="intent" value="save" hidden>Entwurf speichern</button>
        <input type="hidden" name="version" value="{{ $draft->version }}" id="draft-version">
        <input type="hidden" name="element_index" value="" id="element-index">
        <input type="hidden" name="option_index" value="" id="option-index">
        <input type="hidden" name="target_index" value="" id="target-index">

        @unless($thread)
        <p><label for="draft-title">Titel</label><input id="draft-title" name="title" maxlength="225" required value="{{ old('title', $draft->title) }}"></p>
        <p><label for="draft-board">Forum</label><select id="draft-board" name="board" required>
            <option value="">Forum wählen</option>
            @foreach($boards as $board)<option value="{{ $board->id }}" @selected((int) old('board', $draft->board_id) === $board->id)>{{ $board->name }}</option>@endforeach
        </select></p>
        @endunless

        <fieldset class="composer-character"><legend>Charakter</legend>
            @foreach($characters as $character)<label><input type="radio" name="character" value="{{ $character->id }}" @checked((int) old('character', $draft->character_id) === $character->id) required> {{ $character->name }}</label>@endforeach
        </fieldset>

        @php($composerElements = old('elements', $draft->payload))
        <div class="composer-elements" id="composer-elements">
            @foreach($composerElements as $index => $element)
                @include('draft._element', ['element' => $element, 'index' => $index, 'elementCount' => count($composerElements)])
            @endforeach
        </div>

        <div class="composer-add">
            <button type="submit" name="intent" value="add_message">Text hinzufügen</button>
            @if($canTransfer && $endsInsideScene)<button type="submit" name="intent" value="add_transfer">Handlung hinzufügen</button>@endif
            @if($canSetScene)<button type="submit" name="intent" value="add_scene_start">Szene beginnen</button>@endif
            @if($canEndScene && $endsInsideScene)<button type="submit" name="intent" value="add_scene_end">Szene beenden</button>@endif
            @if($canCreatePoll && ! $endsInsideScene)<button type="submit" name="intent" value="add_poll">Umfrage hinzufügen</button>@endif
        </div>
        <div class="composer-submit">
            <button type="submit" name="intent" value="save">Entwurf speichern</button>
            <button type="submit" name="intent" value="publish">Veröffentlichen</button>
            <span id="draft-save-status">
                @if($draft->exists)Gespeichert <x-datetime :time="$draft->updated_at" />@else Noch nicht gespeichert @endif
            </span>
        </div>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('post-composer');
            const version = document.getElementById('draft-version');
            const status = document.getElementById('draft-save-status');
            let timer;
            let dragging;
            const validTargets = @json($moveTargets);
            const save = () => {
                const data = new FormData(form);
                data.set('intent', 'autosave');
                fetch(form.action, {method: 'POST', body: data, headers: {Accept: 'application/json'}})
                    .then(response => response.ok ? response.json() : Promise.reject())
                    .then(result => {
                        version.value = result.version;
                        if (result.action) form.action = result.action;
                        status.textContent = result.saved_at
                            ? 'Gespeichert um ' + new Date(result.saved_at).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})
                            : 'Leere Entwürfe werden nicht gespeichert';
                    })
                    .catch(() => { status.textContent = 'Nicht gespeichert'; });
            };
            form.addEventListener('input', event => {
                if (event.target.name === 'version') return;
                clearTimeout(timer);
                status.textContent = 'Ungespeicherte Änderungen';
                timer = setTimeout(save, 1200);
            });
            form.addEventListener('submit', () => clearTimeout(timer));

            document.querySelectorAll('.composer-element').forEach((element) => {
                element.draggable = true;
                element.addEventListener('dragstart', () => {
                    dragging = Number(element.dataset.index);
                    element.classList.add('is-dragging');
                    document.querySelectorAll('.composer-element').forEach((target) => {
                        target.classList.toggle('is-valid-drop', (validTargets[dragging] || []).includes(Number(target.dataset.index)));
                    });
                });
                element.addEventListener('dragend', () => {
                    element.classList.remove('is-dragging');
                    document.querySelectorAll('.composer-element').forEach(target => target.classList.remove('is-valid-drop'));
                });
                element.addEventListener('dragover', event => {
                    if ((validTargets[dragging] || []).includes(Number(element.dataset.index))) event.preventDefault();
                });
                element.addEventListener('drop', event => {
                    event.preventDefault();
                    const target = Number(element.dataset.index);
                    if (!Number.isInteger(dragging) || dragging === target) return;
                    if (!(validTargets[dragging] || []).includes(target)) {
                        status.textContent = 'Diese Position liegt außerhalb des erlaubten Szenenbereichs';
                        return;
                    }
                    clearTimeout(timer);
                    const data = new FormData(form);
                    data.set('intent', 'move');
                    data.set('element_index', dragging);
                    data.set('target_index', target);
                    status.textContent = 'Reihenfolge wird geprüft';
                    fetch(form.action, {method: 'POST', body: data, headers: {Accept: 'application/json'}})
                        .then(response => response.ok ? response.json() : response.json().then(body => Promise.reject(body)))
                        .then(() => window.location.reload())
                        .catch(result => {
                            status.textContent = result?.message ?? 'Diese Position ist nicht erlaubt';
                        });
                });
            });

            const updateTransferFields = (element) => {
                const action = element.querySelector('[name$="[transfer_action]"]')?.value;
                if (!action) return;
                const character = form.querySelector('[name="character"]:checked')?.value;
                const siteField = element.querySelector('.composer-company-site');
                const site = siteField?.querySelector('select')?.value;
                const usesCompany = ['company_deposit', 'company_withdrawal'].includes(action);
                if (siteField) siteField.hidden = !usesCompany;
                element.querySelector('.character-selector')?.toggleAttribute('hidden', !['give', 'company_withdrawal'].includes(action));
                element.querySelectorAll('.composer-inventory').forEach((inventory) => {
                    const visible = (inventory.dataset.source === 'character' && inventory.dataset.owner === character && ['give', 'drop', 'company_deposit'].includes(action))
                        || (inventory.dataset.source === 'location' && action === 'pickup')
                        || (inventory.dataset.source === 'company' && inventory.dataset.owner === site && action === 'company_withdrawal');
                    inventory.hidden = !visible;
                    inventory.querySelectorAll('input').forEach(input => input.disabled = !visible);
                });
            };
            document.querySelectorAll('.composer-element-transfer').forEach((element) => {
                element.querySelector('[name$="[transfer_action]"]')?.addEventListener('change', () => updateTransferFields(element));
                element.querySelector('.composer-company-site select')?.addEventListener('change', () => updateTransferFields(element));
                updateTransferFields(element);
            });
            form.querySelectorAll('[name="character"]').forEach((input) => input.addEventListener('change', () => {
                document.querySelectorAll('.composer-element-transfer').forEach(updateTransferFields);
            }));
        });
    </script>
