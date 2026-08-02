<x-main-layout :title="'Recht »'.($permission->permit?->name ?? '#'.$permission->permit_id).'« bearbeiten'" alt-title="Recht bearbeiten" css="thread">
    <div class="post">
        <form name="editpermission" action="{{ route('permission.edit', $permission) }}" method="post">
            @csrf

            <p>
                <label for="recipient_type">Art des Rechteempfängers</label>
                <select name="recipient_type" id="recipient_type" required>
                    @foreach ($recipientTypes as $value => $label)
                    <option value="{{ $value }}" @selected((int) old('recipient_type', $permission->recipient_type) === $value)>{{ $label }} ({{ $value }})</option>
                    @endforeach
                </select>
            </p>

            <p>
                <label for="recipient_id">ID des Rechteempfängers</label>
                <input type="number" name="recipient_id" id="recipient_id" value="{{ old('recipient_id', $permission->recipient_id) }}" min="0" required>
            </p>

            <p>
                <label for="permit_id">Recht</label>
                <select name="permit_id" id="permit_id" required>
                    @foreach ($permits as $permit)
                    <option value="{{ $permit->id }}" @selected((int) old('permit_id', $permission->permit_id) === $permit->id)>{{ $permit->name }} ({{ $permit->id }})</option>
                    @endforeach
                </select>
            </p>

            <p>
                <label for="value">Wert</label>
                <select name="value" id="value" required>
                    <option value="0" @selected((string) old('value', $permission->value) === '0')>0 - nein</option>
                    <option value="1" @selected((string) old('value', $permission->value) === '1')>1 - eigene</option>
                    <option value="2" @selected((string) old('value', $permission->value) === '2')>2 - alle</option>
                </select>
            </p>

            <input type="submit" value="Recht bearbeiten">
        </form>
    </div>
</x-main-layout>
