@foreach ($territories as $child)
<li>
    <a href="{{ route('territory.view', $child) }}">{{ $child->displayName() }}</a>
    @if ($child->ruler)
    <p class="small">
        <a href="{{ route('user.character', $child->ruler) }}">{{ $child->ruler->name }}</a>
    </p>
    @endif
</li>
@endforeach
