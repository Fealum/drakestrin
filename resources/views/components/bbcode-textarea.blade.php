@props([
    'name' => 'message',
    'id' => null,
    'value' => '',
    'required' => false,
    'placeholder' => null,
    'label' => null,
    'autogrow' => false,
    'toolbar' => true,
])

@php
    $textareaId = $id ?? 'bbcode-'.preg_replace('/[^a-z0-9_-]+/i', '-', $name);
@endphp

@once
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

<div @class(['bbcode-editor', 'bbcode-editor-autogrow' => $autogrow]) x-data="bbcodeTextarea()">
    @if($toolbar)<x-bbcode-toolbar />@endif

    <textarea
        x-ref="textarea"
        class="textarea-bbcode"
        name="{{ $name }}"
        id="{{ $textareaId }}"
        @if($placeholder !== null) placeholder="{{ $placeholder }}" @endif
        @if($label !== null) aria-label="{{ $label }}" @endif
        @required($required)
    >{{ $value }}</textarea>
</div>
