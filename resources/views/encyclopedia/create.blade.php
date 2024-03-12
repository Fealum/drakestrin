<x-main-layout :title="'Unterseite zu »'.$page->name.'« anlegen'" css="encyclopedia_view">
    <form name="createEncyclopedia" action="{{ route('encyclopedia.create', ['page' => $page->id]) }}" method="post">
        @csrf
        <x-textinput formname="createEncyclopedia" inputname="name" displayname="Seitentitel (kurz)" :value="old('name')" :required="true"/>
        <x-textinput formname="createEncyclopedia" inputname="title" displayname="Seitentitel (lang)" :value="old('title')" :required="true"/>
        <x-textinput formname="createEncyclopedia" inputname="sort" displayname="Sortierungsrang" type="number" :value="old('sort')"/>
        <textarea name="text"></textarea>
        <p><input type="submit" value="Neue Seite erstellen" name="submit" /></p>
    </form>
</x-main-layout>