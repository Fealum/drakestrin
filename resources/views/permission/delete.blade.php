<x-main-layout :title="'Recht »'.($permission->permit_legacy?->name ?? '#'.$permission->permit).'« löschen'" alt-title="Recht löschen" css="thread">
    <p>
        Soll das Recht
        {{ $permission->recipientName() }}:
        {{ $permission->permit_legacy?->name ?? '#'.$permission->permit }}
        ({{ $permission->value }})
        wirklich gelöscht werden?
    </p>

    <form name="deletepermission" action="{{ route('permission.delete', ['permission' => $permission->id]) }}" method="post">
        @csrf
        <input type="hidden" name="delete" value="1">
        <input type="submit" value="Recht löschen">
    </form>
</x-main-layout>
