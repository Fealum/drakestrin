<ol>
@foreach ($pages as $page)
    <li class="encyclopedia-{{ $page->id }}">
        @if (!$page->parent)
        <img src="{{ asset('img/encyclopedia/thumb-'.$page->id.'.jpg') }}" />
        @endif
        <a href="{{ route('encyclopedia.view', ['page' => $page->id]) }}"
            @if (strlen($page->text) < 5)
            class="stub"
            @endif
        >{{ $page->name }}</a>
	@if (count($page->children) > 0)
        <x-encyclopedia.subpages :pages="$page->children"/>
    @endif
	</li>
@endforeach
</ol>