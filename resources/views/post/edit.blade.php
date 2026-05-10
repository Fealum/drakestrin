<x-main-layout :title="'Beitrag im Thema »'.$post->threadModel->name.'« bearbeiten'" alt-title="Beitrag bearbeiten" css="thread">
    <div class="post">
        <form name="editpost" action="{{ route('post.update', ['post' => $post->id]) }}" method="post">
            @csrf
            <div class="post-charselect">
                <ul>
                    @foreach ($characters as $character)
                    <li>
                        <input name="character" value="{{ $character->id }}" id="char-{{ $character->id }}" type="radio" @checked($post->character === $character->id)>
                        <label for="char-{{ $character->id }}">
                            <img src="{{ url('/img/character_avatar.id/thumb/'.$character->avatarThumbPath().'.jpg') }}" title="{{ $character->name }}" alt="{{ $character->name }}">
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>
            <x-bbcode-textarea name="message" :value="$post->message" />
            <input type="submit" value="Beitrag bearbeiten">
        </form>
    </div>
</x-main-layout>
