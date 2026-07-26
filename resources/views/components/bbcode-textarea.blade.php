@props([
    'name' => 'message',
    'id' => null,
    'value' => '',
    'required' => false,
])

@php
    $textareaId = $id ?? 'bbcode-'.preg_replace('/[^a-z0-9_-]+/i', '-', $name);
    $emoticons = \App\Services\Board\ForumFormatter::emoticons();
@endphp

@once
<script defer src="{{ asset('js/alpine.min.js') }}"></script>
<script>
    window.bbcodeTextarea = function () {
        return {
            smiliesOpen: false,
            insert(open, close = '', placeholder = '') {
                const textarea = this.$refs.textarea;
                const start = textarea.selectionStart ?? textarea.value.length;
                const end = textarea.selectionEnd ?? start;
                const selected = textarea.value.slice(start, end);
                const content = selected || placeholder;
                const insertion = open + content + close;

                textarea.value = textarea.value.slice(0, start) + insertion + textarea.value.slice(end);
                textarea.focus();

                const cursor = selected
                    ? start + insertion.length
                    : start + open.length + content.length;

                textarea.selectionStart = textarea.selectionEnd = cursor;
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
            },
            insertListItem() {
                this.insert('[list]\n[*]', '\n[/list]', 'Eintrag');
            },
            insertSmiley(code) {
                this.insert(code, '', '');
                this.smiliesOpen = false;
            },
        };
    };
</script>
@endonce

<div class="bbcode-editor" x-data="bbcodeTextarea()">
    <div class="bbcode-toolbar" aria-label="BBCode-Werkzeuge">
        <button type="button" title="Fett" aria-label="Fett" x-on:click="insert('[b]', '[/b]', 'Text')"><strong>B</strong></button>
        <button type="button" title="Kursiv" aria-label="Kursiv" x-on:click="insert('[i]', '[/i]', 'Text')"><em>I</em></button>
        <button type="button" title="Unterstrichen" aria-label="Unterstrichen" x-on:click="insert('[u]', '[/u]', 'Text')"><u>U</u></button>
        <button type="button" title="Zitat" aria-label="Zitat" x-on:click="insert('[q]', '[/q]', 'Zitat')">Zitat</button>
        <button type="button" title="Link" aria-label="Link" x-on:click="insert('[url=https://]', '[/url]', 'Linktext')">URL</button>
        <button type="button" title="Bild" aria-label="Bild" x-on:click="insert('[img]', '[/img]', 'https://')">Bild</button>
        <button type="button" title="Liste" aria-label="Liste" x-on:click="insertListItem()">Liste</button>
        <button type="button" title="Handlung" aria-label="Handlung" x-on:click="insert('[h]', '[/h]', 'Handlung')">H</button>
        <button type="button" title="SimOff" aria-label="SimOff" x-on:click="insert('[s]', '[/s]', 'SimOff')">S</button>
        <span class="bbcode-smilies">
            <button type="button" title="Smileys" aria-label="Smileys" x-on:click="smiliesOpen = ! smiliesOpen">:)</button>
            <span class="bbcode-smiley-picker" x-cloak x-show="smiliesOpen" x-on:click.outside="smiliesOpen = false">
                @foreach ($emoticons as $code => [$file, $title])
                <button type="button" title="{{ $title ?: $code }}" aria-label="{{ $title ?: $code }}" x-on:click="insertSmiley(@js($code))">
                    <img src="{{ asset('images/emoticon/'.$file) }}" alt="{{ $code }}">
                </button>
                @endforeach
            </span>
        </span>
    </div>

    <textarea
        x-ref="textarea"
        class="textarea-bbcode"
        name="{{ $name }}"
        id="{{ $textareaId }}"
        @required($required)
    >{{ $value }}</textarea>
</div>
