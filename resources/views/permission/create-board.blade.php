<x-main-layout :title="'Neues Recht für »'.$board->name.'« erstellen'" css="thread">
    <div class="post">
        <form name="createpermission" action="{{ route('permission.create_board', ['board' => $board->id]) }}" method="post">
            @csrf

            <p>
                <label for="table__recipient">Art des Rechteempfängers</label>
                <select name="table__recipient" id="table__recipient" required>
                    @foreach ($recipientTypes as $value => $label)
                    <option value="{{ $value }}" @selected((int) old('table__recipient', 4) === $value)>{{ $label }} ({{ $value }})</option>
                    @endforeach
                </select>
            </p>

            <p>
                <label for="recipient">ID des Rechteempfängers</label>
                <input type="number" name="recipient" id="recipient" value="{{ old('recipient') }}" min="0" required>
            </p>

            <p>
                <label for="permit">Recht</label>
                <select name="permit" id="permit" required>
                    @foreach ($permits as $permit)
                    <option value="{{ $permit->id }}" @selected((int) old('permit') === $permit->id)>{{ $permit->name }} ({{ $permit->id }})</option>
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
