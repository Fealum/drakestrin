<x-main-layout :title="'»'.$user->name.'« bearbeiten'" alt-title="Nutzer bearbeiten" css="user_edit">
    <form name="edituser" action="{{ route('user.edit', $user->id) }}" method="post">
        @csrf

        <h3>Avatar</h3>
        <p>Avatar des folgenden Charakters als Nutzeravatar benutzen</p>
        @if ($user->characters->isNotEmpty())
        <div class="edit-avatarselect">
            <ul>
                @foreach ($user->characters as $character)
                <li>
                    <input name="character__avatar" value="{{ $character->id }}" id="character__avatar-{{ $character->id }}" type="radio" @checked((int) old('character__avatar', $user->character__avatar) === $character->id)>
                    <label for="character__avatar-{{ $character->id }}"><x-avatar :subject="$character" size="list" :title="$character->name" /></label>
                </li>
                @endforeach
            </ul>
        </div>
        @else
        <p>Keine Charaktere vorhanden.</p>
        @endif

        <h3>Informationen</h3>
        <p><textarea name="usertext">{{ old('usertext', $user->usertext) }}</textarea></p>
        <p>
            <label for="gender">Geschlecht: </label>
            <select name="gender" id="gender">
                <option label="keine Angabe" value="0" @selected((int) old('gender', $user->gender) === 0)>keine Angabe</option>
                <option label="männlich" value="1" @selected((int) old('gender', $user->gender) === 1)>männlich</option>
                <option label="weiblich" value="2" @selected((int) old('gender', $user->gender) === 2)>weiblich</option>
                <option label="anderes" value="3" @selected((int) old('gender', $user->gender) === 3)>anderes</option>
            </select>
        </p>
        <p>
            <label for="birthday">Geburtsjahr</label>
            <input type="text" name="birthday" id="birthday" value="{{ old('birthday', $user->birthday) }}">
            @error('birthday') <span class="small">{{ $message }}</span> @enderror
        </p>
        <p>
            <label for="location">Herkunft</label>
            <input type="text" name="location" id="location" value="{{ old('location', $user->location) }}">
        </p>
        <p>
            <label for="interests">Interessen</label>
            <input type="text" name="interests" id="interests" value="{{ old('interests', $user->interests) }}">
        </p>
        <p>
            <label for="work">Amt oder Beruf</label>
            <input type="text" name="work" id="work" value="{{ old('work', $user->work) }}">
        </p>
        <input type="hidden" name="edit" value="1">
        <input type="submit" value="Ändern">
    </form>

    <h3>Nutzerkonto</h3>
    <em>Beiträge:</em> {{ number_format($user->post__total ?? 0, 0, ',', '.') }}<br>
    <em>Mitglied seit:</em> <x-datetime :time="$user->regdate" /><br>
</x-main-layout>
