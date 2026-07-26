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
@endphp

<img
    src="{{ $url }}"
    alt="{{ $alt }}"
    @if($title) title="{{ $title }}" @endif
    {{ $attributes
        ->class(['avatar', 'avatar-'.$size])
    }}
>
