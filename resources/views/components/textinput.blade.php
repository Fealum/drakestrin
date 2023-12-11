<!-- resources/views/components/textinput.blade.php -->

@props(['formname', 'inputname', 'displayname', 'type' => 'text', 'maxlength' => false, 'autofocus' => false, 'required' => false, 'value' => null, 'placeholder' => null])

<p>
    <label for="{{ $inputname }}">{{ $displayname }}: </label>
    <input type="{{ $type }}" name="{{ $inputname }}" id="{{ $inputname }}"
        @if ($maxlength) maxlength="{{ $maxlength }}" @endif
        @if ($value) value="{{ $value }}" @elseif (isset(${$formname . $inputname})) value="{{ ${$formname . $inputname} }}" @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($autofocus) autofocus @endif
        @if ($required) required @endif
    />
    @error($inputname)
    <span class="alert">{{ $message }}</span>
    @enderror
</p>