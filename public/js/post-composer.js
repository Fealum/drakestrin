document.addEventListener('alpine:init', () => {
    Alpine.data('locationPicker', () => ({
        status: '', timer: null, controller: null,
        scheduleSearch() { clearTimeout(this.timer); this.timer = setTimeout(() => this.search(1), 350); },
        destroy() { clearTimeout(this.timer); this.controller?.abort(); },
        async search(page = 1) {
            clearTimeout(this.timer);
            this.controller?.abort();
            const controller = this.controller = new AbortController();
            const url = new URL(this.$el.closest('[data-search-url]').dataset.searchUrl, window.location.href);
            const root = this.$refs.results.closest('[data-search-url]');
            url.search = new URLSearchParams({ q: this.$refs.query.value, page, index: root.dataset.index,
                selected: this.$refs.results.querySelector('input:checked')?.value || '' });
            this.status = 'Orte werden gesucht ...';
            try {
                const response = await fetch(url, { signal: controller.signal, headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error('search failed');
                const html = await response.text();
                if (controller !== this.controller || controller.signal.aborted) return;
                const template = document.createElement('template');
                template.innerHTML = html;
                Alpine.mutateDom(() => {
                    [...this.$refs.results.children].forEach((child) => Alpine.destroyTree(child));
                    this.$refs.results.replaceChildren(template.content);
                    [...this.$refs.results.children].forEach((child) => Alpine.initTree(child));
                });
                this.status = '';
            } catch (error) {
                if (error.name !== 'AbortError') this.status = 'Suche fehlgeschlagen. Bitte erneut versuchen.';
            }
        },
    }));
    Alpine.data('entityPicker', () => ({
        query: '',
        matches: 0,
        available: 0,
        init() {
            this.filter();
        },
        filter() {
            const choices = [...this.$refs.choices.querySelectorAll('.entity-picker-option')];
            this.available = choices.filter((option) => !option.querySelector('input[type="radio"]').disabled).length;
            if (this.available <= 6) this.query = '';
            const query = this.query.trim().toLocaleLowerCase('de');
            let matches = 0;
            choices.forEach((option) => {
                const radio = option.querySelector('input[type="radio"]');
                const matchesQuery = option.dataset.search.toLocaleLowerCase('de').includes(query);
                if (!radio.disabled && matchesQuery) matches++;
                option.hidden = radio.disabled || (!matchesQuery && !radio.checked);
            });
            this.matches = matches;
        },
    }));

    Alpine.data('postComposer', () => ({
        busy: false,
        saving: false,
        conflict: false,
        revision: 0,
        savedRevision: 0,
        timer: null,
        pendingSave: null,
        status: '',
        resumeUrl: '',
        smiliesOpen: false,
        activeTextarea: null,
        selectionStart: 0,
        selectionEnd: 0,
        activeScene: false,
        activeStoryTime: false,
        canStartScene: false,
        canEndScene: false,
        headerObserver: null,
        scrollListener: null,
        unloadListener: null,
        allowNavigation: false,
        activeMarkerOptions: [],
        commandFailed: false,

        init() {
            // Fragment parsers can turn noscript contents into live form controls.
            this.$refs.form.querySelectorAll('noscript').forEach((element) => element.remove());
            this.status = this.$refs.status.textContent.trim();
            this.resumeUrl = this.$refs.resume.href;
            this.updateAuthor();
            this.updateTransfers();
            this.scrollListener = () => {
                const toolbar = this.$refs.form.querySelector('.composer-toolbar');
                const headerHeight = document.querySelector('body > header')?.getBoundingClientRect().height || 60;
                toolbar?.classList.toggle('is-stuck', this.$refs.form.querySelector('.composer-elements').getBoundingClientRect().top < headerHeight);
            };
            window.addEventListener('scroll', this.scrollListener, { passive: true });
            this.unloadListener = (event) => {
                if (this.allowNavigation || (this.revision === this.savedRevision && !this.busy)) return;
                event.preventDefault();
                event.returnValue = '';
            };
            window.addEventListener('beforeunload', this.unloadListener);
            this.$nextTick(this.scrollListener);
            this.rememberSelection({ target: this.$refs.form.querySelector('.composer-element-message textarea') });
            const header = document.querySelector('body > header');
            if (header && window.ResizeObserver) {
                this.headerObserver = new ResizeObserver(() => {
                    this.$refs.form.style.setProperty('--composer-header-height', header.getBoundingClientRect().height + 'px');
                });
                this.headerObserver.observe(header);
            }
        },

        destroy() {
            clearTimeout(this.timer);
            this.headerObserver?.disconnect();
            window.removeEventListener('scroll', this.scrollListener);
            window.removeEventListener('beforeunload', this.unloadListener);
        },

        rememberSelection(event) {
            const textarea = event?.target;
            if (!textarea?.matches('.composer-element-message textarea')) return;
            this.activeTextarea = textarea;
            this.selectionStart = textarea.selectionStart;
            this.selectionEnd = textarea.selectionEnd;
            const context = textarea.closest('.composer-element').dataset;
            this.activeScene = context.sceneActive === '1';
            this.activeStoryTime = context.storyTime !== '';
            this.canStartScene = context.canStart === '1';
            this.canEndScene = context.canEnd === '1';
            this.activeMarkerOptions = (context.markerOptions || '').split(',');
        },

        insert(open, close = '', placeholder = '') {
            const textarea = this.activeTextarea;
            if (!textarea?.isConnected || this.busy) return;
            const start = this.selectionStart;
            const end = this.selectionEnd;
            const selected = textarea.value.slice(start, end);
            const content = selected || placeholder;
            textarea.setRangeText(open + content + close, start, end, 'end');
            textarea.focus({ preventScroll: true });
            const caret = selected ? start + open.length + content.length + close.length : start + open.length + content.length;
            textarea.setSelectionRange(caret, caret);
            this.rememberSelection({ target: textarea });
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
        },

        insertListItem() { this.insert('[list]\n[*]', '\n[/list]', 'Eintrag'); },
        insertSmiley(code) { this.insert(code); this.smiliesOpen = false; },

        markerAllowed(type) {
            if (!this.activeTextarea || this.conflict) return false;
            if (type === 'transfer') return this.activeMarkerOptions.includes('transfer');
            if (type === 'poll') return this.activeMarkerOptions.includes('poll');
            return (this.activeMarkerOptions.includes('scene_end') && this.canEndScene)
                || (this.activeMarkerOptions.includes('scene_start') && this.canStartScene);
        },

        async insertMarker(type) {
            if (!this.markerAllowed(type) || this.busy) return;
            const existing = new Set([...this.$refs.form.querySelectorAll('[data-editor-key]')].map((element) => element.dataset.editorKey));
            // A block is inserted at the start of a selection, without discarding selected prose.
            const insertion = { textarea: this.activeTextarea, offset: this.selectionStart, marker: '[' + type + ' /]', label: { transfer: 'Übergabe', poll: 'Umfrage', scene: 'Szene' }[type] };
            this.selectionEnd = this.selectionStart;
            this.insert(insertion.marker);
            await this.submit(null, 'save', insertion);
            const composer = document.querySelector('#post-composer');
            const block = [...(composer?.querySelectorAll('[data-editor-key]') || [])]
                .find((element) => element.dataset.editorKey && !existing.has(element.dataset.editorKey));
            if (block) {
                const editor = block.querySelector('details');
                if (editor) editor.open = true;
                const input = block.querySelector('input:not([type="hidden"]):not(:disabled), select:not(:disabled)');
                block.scrollIntoView({ block: 'center' });
                input?.focus({ preventScroll: true });
            }
        },

        changed(event) {
            this.rememberSelection(event);
            const block = event?.target?.closest('.composer-element');
            if (block && !block.classList.contains('composer-element-message')) {
                block.dataset.previewDirty = 'true';
                const editor = block.querySelector('details');
                if (editor) editor.open = true;
            }
            if (event?.target?.name === 'newcharname') this.updateAuthor();
            this.revision++;
            if (!this.conflict) this.status = 'Ungespeicherte Änderungen';
            clearTimeout(this.timer);
            if (!this.busy && !this.conflict)
                this.timer = setTimeout(() => this.autosave(), 1200);
        },

        async autosave() {
            if (this.busy || this.conflict || this.pendingSave) return;
            const revision = this.revision;
            const form = this.$refs.form;
            const data = new FormData(form);
            data.set('intent', 'autosave');
            this.saving = true;
            this.status = 'Wird gespeichert …';
            this.pendingSave = (async () => {
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: data,
                        headers: { Accept: 'application/json' },
                    });
                    const result = await response.json();
                    if (!response.ok) {
                        this.conflict = Boolean(result.errors?.draft);
                        if (!this.conflict && Number.isInteger(result.version)) {
                            form.elements.version.value = result.version;
                        }
                        this.status =
                            Object.values(result.errors || {})
                                .flat()
                                .join(' ') || 'Speichern fehlgeschlagen.';
                        return;
                    }
                    form.elements.version.value = result.version;
                    if (result.action) form.action = result.action;
                    if (result.resume_url) this.resumeUrl = result.resume_url;
                    this.savedRevision = revision;
                    if (this.revision === revision && result.controls) this.updateControls(result.controls);
                    this.status = this.revision !== revision
                        ? 'Ungespeicherte Änderungen'
                        : result.saved_at
                        ? 'Gespeichert um ' +
                          new Date(result.saved_at).toLocaleTimeString([], {
                              hour: '2-digit',
                              minute: '2-digit',
                          })
                        : 'Leere Entwürfe werden nicht gespeichert';
                } catch (_) {
                    this.status = 'Nicht gespeichert. Bitte erneut speichern.';
                } finally {
                    this.saving = false;
                }
            })();
            await this.pendingSave;
            this.pendingSave = null;
            if (!this.busy && !this.conflict && this.revision !== revision) {
                this.timer = setTimeout(() => this.autosave(), 1200);
            }
        },

        async submit(event, intent = null, insertion = null) {
            if (this.busy || this.conflict) return;
            clearTimeout(this.timer);
            this.busy = true;
            this.commandFailed = false;
            const form = this.$refs.form;
            const noValidate = form.noValidate;
            const submittedIntent = intent ?? event?.submitter?.value ?? 'save';
            let feedback = null;
            let navigating = false;
            const beforeLoad = (event) => {
                if (event.detail.xhr.getResponseHeader('HX-Redirect')) {
                    navigating = true;
                    this.allowNavigation = true;
                }
            };
            form.addEventListener('htmx:beforeOnLoad', beforeLoad);
            // Prevent edits during a command whose response replaces the composer.
            form.inert = true;
            try {
                this.status = 'Wird verarbeitet …';
                feedback = this.commandFeedback(event?.submitter, submittedIntent, insertion);
                if (this.pendingSave) await this.pendingSave;
                if (this.conflict) return;
                if (!window.htmx) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'intent';
                    input.value = submittedIntent;
                    form.append(input);
                    this.allowNavigation = true;
                    HTMLFormElement.prototype.submit.call(form);
                    navigating = true;
                    return;
                }
                this.status = 'Wird verarbeitet …';
                // Publication is validated server-side after pending edits and marker conversion.
                form.noValidate = true;
                await htmx.ajax('POST', form.action, {
                    source: form,
                    target: '#newpost',
                    swap: 'outerHTML',
                    values: {
                        intent: submittedIntent,
                    },
                });
                if (form.isConnected && !navigating && !this.commandFailed && !this.conflict) {
                    this.status = submittedIntent === 'publish'
                        ? 'Veröffentlichung nicht bestätigt. Bitte prüfe das Thema, bevor du erneut veröffentlichst.'
                        : 'Die Änderung konnte nicht bestätigt werden. Deine Eingaben bleiben erhalten; bitte erneut versuchen.';
                }
            } catch (_) {
                if (!this.conflict)
                    this.status = submittedIntent === 'publish'
                        ? 'Veröffentlichung nicht bestätigt. Bitte prüfe das Thema, bevor du erneut veröffentlichst.'
                        : 'Die Anfrage ist fehlgeschlagen. Deine Eingaben bleiben erhalten.';
            } finally {
                feedback?.restore(form.isConnected && !navigating && !this.commandFailed && !this.conflict);
                form.removeEventListener('htmx:beforeOnLoad', beforeLoad);
                form.noValidate = noValidate;
                if (!navigating) {
                    form.inert = false;
                    this.busy = false;
                }
            }
        },

        updateControls(controls) {
            this.$refs.form.querySelectorAll('.composer-element').forEach((block) => {
                const index = Number(block.dataset.index);
                block.dataset.markerOptions = (controls.markers[index] || []).join(',');
                const targets = controls.moves[index] || [];
                const up = targets.filter((target) => target < index).at(-1);
                const down = targets.find((target) => target > index);
                for (const [direction, target] of [['up', up], ['down', down]]) {
                    const button = block.querySelector('[data-direction="' + direction + '"]');
                    if (!button) continue;
                    button.disabled = target === undefined;
                    button.value = target === undefined ? '' : 'move:' + index + ':' + target;
                }
            });
            this.rememberSelection({ target: this.activeTextarea });
        },

        commandFeedback(button, intent, insertion) {
            this.$refs.form.querySelectorAll('.composer-command-error').forEach((notice) => notice.remove());
            const feedback = document.createElement('div');
            feedback.className = 'composer-loading';
            feedback.setAttribute('role', 'status');
            const spinner = document.createElement('span');
            spinner.className = 'composer-spinner';
            spinner.setAttribute('aria-hidden', 'true');
            feedback.append(spinner);
            const undo = [];
            const blocks = [...this.$refs.form.querySelectorAll('.composer-element')];
            if (insertion) {
                const textarea = insertion.textarea;
                const preview = document.createElement('div');
                preview.className = 'composer-insertion-preview';
                const before = document.createElement('div');
                const after = document.createElement('div');
                before.className = after.className = 'composer-pending-text';
                before.textContent = textarea.value.slice(0, insertion.offset);
                after.textContent = textarea.value.slice(insertion.offset + insertion.marker.length);
                feedback.dataset.pendingType = insertion.label;
                feedback.append(document.createTextNode(insertion.label + ' wird eingefügt ...'));
                preview.append(before, feedback, after);
                const display = textarea.style.display;
                textarea.style.display = 'none';
                textarea.after(preview);
                undo.push(() => { textarea.style.display = display; preview.remove(); });
                feedback.scrollIntoView({ block: 'nearest' });
            } else if (/^remove:\d+$/.test(intent)) {
                const block = blocks.find((element) => element.dataset.index === intent.split(':')[1]);
                if (block) {
                    const display = block.style.display;
                    block.style.display = 'none';
                    feedback.classList.add('composer-loading-compact');
                    feedback.append(document.createTextNode('Baustein wird entfernt ...'));
                    block.after(feedback);
                    undo.push(() => block.style.display = display);
                }
            } else if (/^move:\d+:\d+$/.test(intent)) {
                const [, index, target] = intent.split(':').map(Number);
                const source = blocks.findIndex((element) => Number(element.dataset.index) === index);
                const destination = blocks.findIndex((element) => Number(element.dataset.index) === target);
                if (source >= 0 && destination >= 0) {
                    const reordered = [...blocks];
                    const [moved] = reordered.splice(source, 1);
                    reordered.splice(destination, 0, moved);
                    // Change only visual order: indexed form fields must serialize in their original order.
                    reordered.forEach((element, position) => {
                        const order = element.style.order;
                        element.style.order = position + 1;
                        undo.push(() => element.style.order = order);
                    });
                    feedback.classList.add('composer-loading-compact');
                    feedback.append(document.createTextNode('Baustein wird verschoben ...'));
                    moved.append(feedback);
                    moved.scrollIntoView({ block: 'nearest' });
                }
            } else {
                feedback.append(document.createTextNode('Wird verarbeitet ...'));
                (button?.closest('.composer-element') || this.$refs.form.querySelector('.composer-submit')).append(feedback);
            }
            const errorAnchor = insertion?.textarea.closest('.composer-element') || button?.closest('.composer-element') || this.$refs.form.querySelector('.composer-submit');
            return { restore(failed) {
                undo.reverse().forEach((restore) => restore());
                feedback.remove();
                if (failed) {
                    const notice = document.createElement('p');
                    notice.className = 'composer-command-error';
                    notice.setAttribute('role', 'alert');
                    notice.textContent = 'Änderung nicht bestätigt. Deine Eingaben sind erhalten. Bitte prüfe den gespeicherten Stand.';
                    errorAnchor.append(notice);
                }
            } };
        },

        response(event) {
            this.commandFailed = true;
            if (event.detail.version !== null)
                this.$refs.form.elements.version.value = event.detail.version;
            if (event.detail.action)
                this.$refs.form.action = event.detail.action;
            if (event.detail.resumeUrl) this.resumeUrl = event.detail.resumeUrl;
            this.conflict = event.detail.status === 409;
            this.status = this.conflict
                ? 'Ein neuerer Entwurf wurde andernorts gespeichert. Deine Eingaben wurden nicht überschrieben.'
                : 'Bitte überprüfe die angezeigten Fehler.';
            this.$refs.form.querySelectorAll('.composer-element-editor').forEach((editor) => editor.open = true);
            requestAnimationFrame(() => {
                const feedback = document.querySelector('#composer-feedback');
                feedback?.setAttribute('tabindex', '-1');
                feedback?.focus();
            });
        },

        contextChanged(event) {
            if (event.target.matches('.composer-scene-place, [name$="[scene_action]"], [name$="[story_at]"]')) {
                this.submit(null, 'save');
                return;
            }
            if (
                !event.target.matches(
                    '[name="character"], .composer-transfer-source',
                )
            )
                return;
            const elements = event.target.matches('[name="character"]')
                ? this.$refs.form.querySelectorAll('.composer-element-transfer')
                : [event.target.closest('.composer-element-transfer')];
            elements.forEach((element) => {
                element
                    .querySelectorAll(
                        'input[type="checkbox"][name*="[inventory]"]',
                    )
                    .forEach((input) => (input.checked = false));
            });
            this.updateAuthor();
            this.updateTransfers();
            if (event.target.matches('[name="character"]')) this.submit(null, 'save');
            else this.changed();
        },

        leftBlock(event) {
            const block = event.target.closest('.composer-element-transfer');
            const destination = event.relatedTarget;
            // Label clicks and hidden radio updates can clear focus without leaving the editor.
            if (!block || !destination || destination === document.body || destination === document.documentElement
                || block.contains(destination) || this.busy || this.conflict) return;
            requestAnimationFrame(() => {
                if (!block.isConnected || block.contains(document.activeElement) || this.busy) return;
                const source = block.querySelector('.composer-transfer-source:checked:not(:disabled)');
                const recipient = block.querySelector('.composer-transfer-recipient:checked:not(:disabled)');
                const selected = [...block.querySelectorAll('.composer-inventory:not([hidden]) input[type="checkbox"]:checked:not(:disabled)')];
                if (!source || !recipient || selected.length === 0) return;
                const items = selected.map((checkbox) => {
                    const item = checkbox.closest('.composer-action-item').querySelector('.inventory-item-control');
                    const quantity = item.querySelector('input')?.value.trim() || (item.dataset.stackable === '0' ? '1' : '');
                    return { name: item.dataset.itemName, quantity };
                });
                if (items.some((item) => !item.quantity || /^-/.test(item.quantity) || /^0+(?:[.,]0+)?$/.test(item.quantity))) return;
                const preview = document.createElement('div');
                preview.className = 'composer-block-preview';
                const line = document.createElement('div');
                line.className = 'post-transfer composer-transfer-preview';
                const party = (radio) => {
                    const label = radio.closest('.entity-picker-option');
                    const span = document.createElement('span');
                    span.className = 'composer-preview-party';
                    if (label.querySelector('img')) span.append(label.querySelector('img').cloneNode());
                    span.append(document.createTextNode(label.querySelector('.entity-picker-name').textContent));
                    return span;
                };
                const arrow = () => {
                    const icon = document.createElement('span');
                    icon.className = 'fa fa-long-arrow-right';
                    icon.setAttribute('aria-hidden', 'true');
                    return icon;
                };
                const goods = document.createElement('span');
                goods.textContent = items.map((item) => item.name + ' (' + item.quantity + ')').join(', ');
                line.append(party(source), arrow(), goods, arrow(), party(recipient));
                preview.append(line);
                block.querySelector('.composer-block-preview')?.remove();
                const editor = block.querySelector('details');
                editor.after(preview);
                editor.dataset.ready = '1';
                block.dataset.previewDirty = 'false';
                editor.open = false;
            });
        },

        updateAuthor() {
            const form = this.$refs.form;
            const selected = form.querySelector('[name="character"]:checked');
            if (!selected) return;
            const name = selected.value === 'new'
                ? form.querySelector('[name="newcharname"]')?.value.trim() || 'Neuer Charakter'
                : selected.dataset.name;
            const avatar = form.querySelector('.composer-author-portrait');
            if (avatar) {
                avatar.src = selected.dataset.avatar;
                avatar.alt = name;
            }
            form.querySelector('.composer-author-name').textContent = name;
            form.querySelectorAll('[data-current-character]').forEach((radio) => {
                const option = radio.closest('.entity-picker-option');
                option.dataset.search = name;
                option.querySelector('.entity-picker-name').textContent = name;
            });
        },

        updateTransfers() {
            const form = this.$refs.form;
            const character = form.querySelector(
                '[name="character"]:checked',
            )?.value;
            form.querySelectorAll('.composer-element-transfer').forEach(
                (element) => {
                    const sources = element.querySelectorAll('.composer-transfer-source');
                    const recipients = element.querySelectorAll('.composer-transfer-recipient');
                    if (!sources.length || !recipients.length) return;
                    sources.forEach((radio) => {
                        if (radio.dataset.stockCharacterIds !== undefined) {
                            radio.disabled = !radio.dataset.stockCharacterIds.split(',').includes(character);
                        }
                        if (radio.dataset.characterIds !== undefined) {
                            const available = radio.dataset.characterIds
                                .split(',')
                                .includes(character);
                            radio.disabled = !available;
                        }
                        if (radio.checked && radio.disabled) radio.checked = false;
                    });
                    let source = element.querySelector('.composer-transfer-source:checked');
                    if (!source) {
                        source = element.querySelector('.composer-transfer-source[value="character"]:not(:disabled)');
                        if (source) source.checked = true;
                    }
                    const [sourceType, sourceId = ''] = (source?.value || '').split(':');
                    element
                        .querySelectorAll('.composer-inventory')
                        .forEach((inventory) => {
                            const visible =
                                inventory.dataset.source === sourceType &&
                                inventory.dataset.owner ===
                                    (sourceType === 'character'
                                        ? character
                                        : sourceId);
                            inventory.hidden = !visible;
                            inventory.disabled = !visible;
                            inventory
                                .querySelectorAll('input')
                                .forEach(
                                    (input) => (input.disabled = !visible),
                                );
                        });
                    recipients.forEach((radio) => {
                        const [type, id = ''] = radio.value.split(':');
                        radio.disabled =
                            !source || (sourceType === 'location'
                                ? radio.value !== 'character'
                                : (sourceType === 'character' &&
                                      (radio.value === 'character' ||
                                          (type === 'character' &&
                                              id === character))) ||
                                  (type === 'character' && id === character) ||
                                  (sourceType === 'company_site' &&
                                      type === 'company_site' &&
                                      id === sourceId));
                        if (radio.disabled) radio.checked = false;
                        if (sourceType === 'location' && radio.value === 'character') radio.checked = true;
                    });
                    element.querySelectorAll('.entity-picker').forEach((picker) => {
                        picker.dispatchEvent(new CustomEvent('entity-options-updated'));
                    });
                },
            );
        },
    }));
});

document.addEventListener('htmx:beforeSwap', (event) => {
    const source = event.detail.requestConfig?.elt;
    if (source?.id !== 'post-composer') return;
    const status = event.detail.xhr.status;
    if (status === 422 || status === 409) {
        event.detail.shouldSwap = true;
        event.detail.isError = false;
        source.dispatchEvent(
            new CustomEvent('composer-response', {
                detail: {
                    status,
                    version:
                        event.detail.xhr.getResponseHeader('X-Draft-Version'),
                    action: event.detail.xhr.getResponseHeader(
                        'X-Draft-Action',
                    ),
                    resumeUrl:
                        event.detail.xhr.getResponseHeader('X-Draft-Resume'),
                },
            }),
        );
    }
});
