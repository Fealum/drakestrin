<x-main-layout :title="$page->title" css="encyclopedia_view">
    <p>
    @permission('createEncyclopedia', $page)
    <a href="{{ route('encyclopedia.create', $page) }}" class="option create" title="Unterseite erstellen">Unterseite erstellen</a>
    @endpermission
    @permission('editEncyclopedia', $page, auth()->user())
    <a href="{{ route('encyclopedia.edit', $page) }}" class="option edit" title="bearbeiten">bearbeiten</a>
    @endpermission
    @permission('deleteEncyclopedia', $page, auth()->user())
    <a href="{{ route('encyclopedia.delete', $page) }}" class="option delete" title="löschen">löschen</a>
    @endpermission
    </p>
    {!! $page->textFormatted !!}
    @if (count($page->children) > 0)
    <h3>Unterseiten</h3>
    <x-encyclopedia.subpages :pages="$page->children"/>
    @endif
</x-main-layout>
