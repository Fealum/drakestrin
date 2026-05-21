<x-main-layout title="Ort erstellen">
    <form action="{{ route('location.create', ['parentType' => request()->route('parentType'), 'parentId' => request()->route('parentId')]) }}" method="post">
        @csrf
        @include('location._form', [
            'location' => new \App\Models\Territory\Location(),
            'parent' => $parent,
            'submitLabel' => 'Ort erstellen',
        ])
    </form>
</x-main-layout>
