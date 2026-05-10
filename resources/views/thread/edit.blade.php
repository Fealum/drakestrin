<x-main-layout :title="'Thema »'.$thread->name.'« bearbeiten'" alt-title="Thema bearbeiten" css="thread">
    <form name="editthread" action="{{ route('thread.edit', ['thread' => $thread->id]) }}" method="post">
        @csrf

        <p>
            <label for="board">Forum</label>
            <select name="board" id="board" required>
                @foreach ($boards as $option)
                @php($board = $option['board'])
                <option value="{{ $board->id }}" @selected((int) old('board', $thread->board) === $board->id) @disabled($option['disabled'])>{!! str_repeat('&mdash;', $option['level']) !!}{{ $board->name }}</option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="name">Titel</label>
            <input type="text" name="name" id="name" maxlength="225" value="{{ old('name', $thread->name) }}" required>
        </p>

        @if ($canMarkAsImportant)
        <p>
            <input type="checkbox" name="important" value="1" id="important" @checked(old('important', $thread->important))>
            <label for="important">Wichtiges Thema</label>
        </p>
        @endif

        <p><input type="submit" value="Thema bearbeiten"></p>
    </form>
</x-main-layout>
