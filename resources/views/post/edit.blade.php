<x-main-layout :title="'Beitrag im Thema »'.$post->thread->name.'« bearbeiten'" alt-title="Beitrag bearbeiten" css="thread">
    <div class="post">
        <form name="editpost" action="{{ route('post.update', $post) }}" method="post">
            @csrf
            <div class="post-charselect">
                <ul>
                    @foreach ($characters as $character)
                    <li>
                        <input name="character" value="{{ $character->id }}" id="char-{{ $character->id }}" type="radio" @checked($post->character_id === $character->id)>
                        <label for="char-{{ $character->id }}">
                            <x-avatar :subject="$character" size="list" :title="$character->name" />
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>
            @foreach($post->elements->where('type', \App\Support\PostElementType::MESSAGE) as $index => $element)
                @if($element->message)
                <fieldset>
                    <legend>Text {{ $loop->iteration }}</legend>
                    <x-bbcode-textarea :name="'messages['.$index.'][message]'" :value="$element->message->message" />
                    <label><input type="checkbox" name="messages[{{ $index }}][smilies]" value="1" @checked($element->message->smilies)> Smileys anzeigen</label>
                </fieldset>
                @endif
            @endforeach
            <input type="submit" value="Beitrag bearbeiten">
        </form>
    </div>
</x-main-layout>
