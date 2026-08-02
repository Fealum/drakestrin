@foreach ($locations as $location)
<li>
    <a href="{{ route('location.view', $location) }}">{{ $location->name }}</a>
    @if ($location->children->isNotEmpty())
    <ol>
        @include('location._children', ['locations' => $location->children])
    </ol>
    @endif
</li>
@endforeach
