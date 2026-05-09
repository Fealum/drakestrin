@foreach ($territories as $child)
<li>
    <a href="{{ route('territory.view', ['territory' => $child->id]) }}">{{ $child->displayName() }}</a>
    @if ($child->ruler)
    <p class="small">
        <a href="{{ url('/') }}/user/character/{{ $child->ruler->id }}">{{ $child->ruler->name }}</a>
    </p>
    @endif
</li>
@endforeach
