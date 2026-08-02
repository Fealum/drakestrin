<x-main-layout :title="$location->name . ' bearbeiten'">
    <form action="{{ route('location.edit', $location) }}" method="post">
        @csrf
        @include('location._form', [
            'location' => $location,
            'parentOptions' => $parentOptions,
            'selectedParent' => $selectedParent,
            'submitLabel' => 'Ort speichern',
        ])
    </form>
</x-main-layout>
