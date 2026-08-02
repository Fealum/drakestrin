<x-main-layout :title="'Neues Recht für »'.$board->name.'« erstellen'" css="thread">
    <div class="post">
        <form name="createpermission" action="{{ route('permission.create_board', $board) }}" method="post">
            @csrf

            <p>
                <label for="recipient_type">Art des Rechteempfängers</label>
                <select name="recipient_type" id="recipient_type" required>
                    @foreach ($recipientTypes as $value => $label)
                    <option value="{{ $value }}" @selected((int) old('recipient_type', 4) === $value)>{{ $label }} ({{ $value }})</option>
                    @endforeach
                </select>
            </p>

            <p>
                <label for="recipient_id">ID des Rechteempfängers</label>
                <input type="number" name="recipient_id" id="recipient_id" value="{{ old('recipient_id') }}" min="0" required>
            </p>

            <p>
                <label for="permit_id">Recht</label>
                <select name="permit_id" id="permit_id" required>
                    @foreach ($permits as $permit)
                    <option value="{{ $permit->id }}" @selected((int) old('permit_id') === $permit->id)>{{ $permit->name }} ({{ $permit->id }})</option>
                    @endforeach
                </select>
            </p>

            <p>
                <label for="value">Wert</label>
                <select name="value" id="value" required>
                    <option value="0" @selected(old('value') === '0')>0 - nein</option>
                    <option value="1" @selected(old('value') === '1')>1 - eigene</option>
                    <option value="2" @selected(old('value') === '2')>2 - alle</option>
                </select>
            </p>

            <input type="submit" value="Neues Recht erstellen">
        </form>
    </div>
</x-main-layout>
