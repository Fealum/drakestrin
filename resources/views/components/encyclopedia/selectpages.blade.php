@foreach ($pages as $page)
    <option value="{{ $page->id }}"
        @if ($page->id === $obj->id)
        disabled="disabled"
        @endif
        @if ($obj->parent && $page->id === $obj->parent->id)
        selected="selected"
        @endif
        >
        @for ($i = 1; $i < $level; $i++)
        —
        @endfor
        {{ $page->name }}
    </option>
    @if ($page->children && $page->id !== $obj->id)
    <x-encyclopedia.selectpages :pages="$page->children" :level="$level + 1" :obj="$obj"/>
    @endif
@endforeach