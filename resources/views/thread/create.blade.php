<x-main-layout title="Neues Thema erstellen" css="thread">
    @if ($characters->isNotEmpty())
    <div class="post">
        <form name="newthread" action="{{ route('thread.create', $selectedBoard ? ['board' => $selectedBoard->id] : []) }}" method="post">
            @csrf

            <p>
                <label for="board">Forum</label>
                <select name="board" id="board" required>
                    @foreach ($boards as $option)
                    @php($board = $option['board'])
                    <option value="{{ $board->id }}" @selected((int) old('board', $selectedBoard?->id) === $board->id) @disabled($option['disabled'])>{!! str_repeat('&mdash;', $option['level']) !!}{{ $board->name }}</option>
                    @endforeach
                </select>
            </p>

            <p>
                <label for="name">Titel</label>
                <input type="text" name="name" id="name" maxlength="225" value="{{ old('name') }}" required>
            </p>

            @if ($canMarkAsImportant)
            <p>
                <input type="checkbox" name="important" value="1" id="important" @checked(old('important'))>
                <label for="important">Wichtiges Thema</label>
            </p>
            @endif

            <div class="post-charselect">
                <ul>
                    @foreach ($characters as $character)
                    <li>
                        <input name="character" value="{{ $character->id }}" id="char-{{ $character->id }}" type="radio" @checked((int) old('character', $characters->first()->id) === $character->id)>
                        <label for="char-{{ $character->id }}">
                            <x-avatar :subject="$character" size="list" :title="$character->name" />
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>

            <input type="hidden" name="smilies" value="1">
            <input type="hidden" name="signature" value="1">
            <x-bbcode-textarea name="message" :value="old('message')" required />
            <input type="submit" value="Neues Thema erstellen">
        </form>
    </div>
    @else
    <div class="nochar">
        <h3>Noch kein Charakter vorhanden!</h3>
        <p>Um Themen zu erstellen, musst Du zuerst einen Charakter erstellen.</p>
    </div>
    @endif
</x-main-layout>
