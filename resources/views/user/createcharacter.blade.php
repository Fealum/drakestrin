<x-main-layout title="Neuen Charakter erstellen">
    <form name="createcharacter" action="{{ route('user.create_character', $user->id) }}" method="post">
        @csrf
        <p>
            <label for="name">Name</label>
            <input type="text" name="name" id="name" maxlength="85" value="{{ old('name') }}" required>
            @error('name') <span class="small">{{ $message }}</span> @enderror
        </p>
        <p><input type="submit" value="Neuen Charakter erstellen" name="submit"></p>
    </form>
</x-main-layout>
