@props([
    'subject',
    'size' => 'list',
    'alt' => null,
    'title' => null,
])

@php
    $isFull = $size === 'full';
    $url = $isFull && method_exists($subject, 'avatarUrl')
        ? $subject->avatarUrl()
        : $subject->avatarThumbUrl();
    $alt ??= $subject->name ?? '';
    $title ??= null;
    $styles = match ((string) $size) {
        'post', '60' => 'width: 60px; height: 60px;',
        'list', '30' => 'width: 30px; height: 30px;',
        'dropdown', '15' => 'width: 15px; height: 15px; padding: 7.5px;',
        default => null,
    };
@endphp

<img
    src="{{ $url }}"
    alt="{{ $alt }}"
    @if($title) title="{{ $title }}" @endif
    {{ $attributes
        ->class(['avatar', 'avatar-'.$size])
        ->merge($styles ? ['style' => $styles] : []) }}
>
