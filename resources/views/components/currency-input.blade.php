@props([
    'name',
    'oldKey',
    'value' => 0,
])
@php
    $parts = \App\Support\Currency::denominations((int) $value);
    $values = [
        'til' => old($oldKey.'.til', $parts['til']),
        'tuk' => old($oldKey.'.tuk', $parts['tuk']),
        'ten' => old($oldKey.'.ten', $parts['ten']),
    ];
    $displayValue = fn ($amount) => in_array($amount, [null, '', 0, '0'], true) ? '' : $amount;
    $idPrefix = 'currency-'.sha1($name);
@endphp
<span class="currency-input">
    <input id="{{ $idPrefix }}-til" name="{{ $name }}[til]" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="0" autocomplete="off" value="{{ $displayValue($values['til']) }}">
    <label for="{{ $idPrefix }}-til">
        <span class="sr-only">Til</span>
        <span aria-hidden="true">tl</span>
    </label>
    <input id="{{ $idPrefix }}-tuk" name="{{ $name }}[tuk]" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="0" autocomplete="off" value="{{ $displayValue($values['tuk']) }}">
    <label for="{{ $idPrefix }}-tuk">
        <span class="sr-only">Tuk</span>
        <span aria-hidden="true">tk</span>
    </label>
    <input id="{{ $idPrefix }}-ten" name="{{ $name }}[ten]" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="0" autocomplete="off" value="{{ $displayValue($values['ten']) }}">
    <label for="{{ $idPrefix }}-ten">
        <span class="sr-only">Ten</span>
        <span aria-hidden="true">tn</span>
    </label>
</span>
