<x-main-layout title="Forum-Einstellungen" css="board">
    <form method="post" action="{{ route('forum.settings') }}">
        @csrf
        <p>
            <input type="hidden" name="auto_subscribe" value="0">
            <input type="checkbox" id="auto_subscribe" name="auto_subscribe" value="1" @checked(old('auto_subscribe', $preference->auto_subscribe))>
            <label for="auto_subscribe">Themen automatisch abonnieren, wenn ich darin schreibe</label>
        </p>
        <p>
            <label for="default_email_frequency">E-Mail-Vorgabe für neue Abonnements</label>
            <select id="default_email_frequency" name="default_email_frequency">
                @foreach ($frequencies as $frequency)
                <option value="{{ $frequency->value }}" @selected(old('default_email_frequency', $preference->default_email_frequency->value) === $frequency->value)>{{ $frequency->label() }}</option>
                @endforeach
            </select>
        </p>
        <button type="submit" class="fa fa-save"> Einstellungen speichern</button>
    </form>
</x-main-layout>
