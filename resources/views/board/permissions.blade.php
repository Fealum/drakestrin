<x-main-layout :title="'Rechte des Forums »'.$board->name.'«'" css="thread">
    @if ($canCreatePermission)
    <p><a href="{{ route('permission.create_board', ['board' => $board->id]) }}" class="option create" title="neues Recht anlegen">Neues Recht anlegen</a></p>
    @endif

    @foreach ($effectivePermissions as $row)
    <p>
        Du kannst
        @if ((int) $row['value'] === 0)
        keine
        @endif
        {{ $row['label'] }}.
        <span class="small">({{ $row['permit'] }}: {{ $row['value'] }})</span>
    </p>
    @endforeach

    <h3>Spezifische Rechte</h3>
    @forelse ($specificPermissions as $permission)
    <p>
        {{ $permission->recipientName() }}:
        {{ $permission->permit_legacy?->name ?? '#'.$permission->permit }}
        ({{ $permission->value }})
        @if ($canCreatePermission)
        <a class="option edit" title="bearbeiten" href="{{ route('permission.edit', ['permission' => $permission->id]) }}">bearbeiten</a>
        <a class="option delete" title="löschen" href="{{ route('permission.delete', ['permission' => $permission->id]) }}">löschen</a>
        @endif
    </p>
    @empty
    <p>Keine spezifischen Rechte vorhanden.</p>
    @endforelse
</x-main-layout>
