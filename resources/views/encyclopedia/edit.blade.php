<x-main-layout :title="'Seite »'.$page->name.'« bearbeiten'" css="encyclopedia_view">
    <form name="editEncyclopedia" action="{{ route('encyclopedia.edit', ['page' => $page->id]) }}" method="post">
        @csrf
        <x-textinput formname="editEncyclopedia" inputname="name" displayname="Seitentitel (kurz)" :value="old('name') ?? $page->name" :required="true"/>
        <x-textinput formname="editEncyclopedia" inputname="title" displayname="Seitentitel (lang)" :value="old('title') ?? $page->title" :required="true"/>
        <x-textinput formname="editEncyclopedia" inputname="sort" displayname="Sortierungsrang" type="number" :value="old('sort') ?? $page->sort"/>
        <p>
            <label for="parent">Übergeordnete Seite: </label>
            <select name="parent" id="parent">
                <option value="0"
                @if (!isset($page->page_id))
                selected="selected"
                @endif
                >(keine)</option>
                <x-encyclopedia.selectpages :pages="$allPages" :level="0" :obj="$page"/>
            </select>
        </p>
        <textarea name="text">{{ $page->text }}</textarea>
        <p><input type="submit" value="Seite bearbeiten" name="submit" /></p>
    </form>
</x-main-layout>