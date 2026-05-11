<x-main-layout :title="'»'.$character->name.'« bearbeiten'" alt-title="Charakter bearbeiten" css="user_edit">
    <form name="editcharacter" action="{{ route('user.edit_character', $character->id) }}" method="post" enctype="multipart/form-data">
        @csrf

        <h3>Avatar</h3>
        @if ($character->avatar)
        <x-avatar :subject="$character" size="full" id="user_avatar" />
        @endif
        <input type="radio" name="changeavatar" id="avatar-0" value="0" checked>
        <label for="avatar-0" class="labelradio">Avatar nicht ändern</label><br>
        <input type="radio" name="changeavatar" id="avatar-1" value="1">
        <label for="avatar-1" class="labelradio">Folgendes Bild als Avatar verwenden (maximal {{ $avatarMaxKilobytes }} kB):
            <input type="file" name="avatar" id="avatar" accept="image/jpeg, image/gif, image/png">
        </label>
        <input type="hidden" name="MAX_FILE_SIZE" value="{{ $avatarMaxKilobytes * 1000 }}">
        @error('avatar') <p class="notice notice_error">{{ $message }}</p> @enderror

        <h3>Informationen</h3>
        <p><textarea name="usertext">{{ old('usertext', $character->usertext) }}</textarea></p>
        <p>
            <label for="gender">Geschlecht: </label>
            <select name="gender" id="gender">
                <option label="keine Angabe" value="0" @selected((int) old('gender', $character->gender) === 0)>keine Angabe</option>
                <option label="männlich" value="1" @selected((int) old('gender', $character->gender) === 1)>männlich</option>
                <option label="weiblich" value="2" @selected((int) old('gender', $character->gender) === 2)>weiblich</option>
            </select>
        </p>
        <p>
            <label for="birthday">Geburtsjahr</label>
            <input type="text" name="birthday" id="birthday" value="{{ old('birthday', $character->birthday) }}">
            @error('birthday') <span class="small">{{ $message }}</span> @enderror
        </p>
        <p>
            <label for="location">Herkunft</label>
            <input type="text" name="location" id="location" value="{{ old('location', $character->location) }}">
        </p>
        <p>
            <label for="interests">Interessen</label>
            <input type="text" name="interests" id="interests" value="{{ old('interests', $character->interests) }}">
        </p>
        <p>
            <label for="work">Amt oder Beruf</label>
            <input type="text" name="work" id="work" value="{{ old('work', $character->work) }}">
        </p>
        <input type="hidden" name="edit" value="1">
        <input type="submit" value="Ändern">
    </form>

    <h3>Nutzerkonto</h3>
    <em>Beiträge:</em> {{ number_format($character->post__total ?? 0, 0, ',', '.') }}<br>
    <em>Mitglied seit:</em> <x-datetime :time="$character->regdate" /><br>

    @if ($character->inventory->isNotEmpty())
    <h3>Besitz</h3>
    <ol class="inventory">
        @foreach ($character->inventory as $inventory)
        @php($item = $inventory->itemModel)
        @if ($item)
        <li><img src="{{ url('/img/item.img/'.$item->img.'.png') }}" alt=""> {{ $item->name }}: {{ $inventory->stack }}, {{ $inventory->wear }}</li>
        @endif
        @endforeach
    </ol>
    @endif

    @if ($character->companies->isNotEmpty())
    <h3>Betriebe</h3>
    <ol>
        @foreach ($character->companies as $company)
        <li>{{ $company->name }}: {{ $company->description }}</li>
        @endforeach
    </ol>
    @endif
</x-main-layout>
