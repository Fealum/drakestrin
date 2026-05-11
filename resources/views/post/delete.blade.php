@inject('forumFormatter', 'App\Services\Board\ForumFormatter')

<x-main-layout :title="'Beitrag im Thema »'.$post->threadModel->name.'« löschen'" alt-title="Beitrag löschen" css="thread">
    <h3>Sicher?</h3>
    <p>Bist Du Dir sicher, dass Du diesen Beitrag löschen möchtest?</p>
    @if ($deletesThread)
    <p class="notice notice_warning">Dies ist der letzte Beitrag in diesem Thema. Wenn Du ihn löschst, wird auch das Thema gelöscht.</p>
    @endif

    <div class="post">
        @if ($post->characterModel)
        <x-avatar :subject="$post->characterModel" size="list" />
        @endif
        <div class="postuser">
            <h4>
                @if ($post->characterModel)
                <a href="{{ url('/user/character/'.$post->characterModel->id) }}">{{ $post->characterModel->name }}</a>
                @else
                Unbekannter Charakter
                @endif
                <span class="datetime"><x-datetime :time="$post->time" /></span>
            </h4>
        </div>
        <div class="postcontent">{!! $forumFormatter->render($post->message, $post->smilies) !!}</div>
    </div>

    <form name="deletepost" action="{{ route('post.destroy', ['post' => $post->id]) }}" method="post">
        @csrf
        <input type="hidden" name="delete" value="1">
        <p><input type="submit" value="Beitrag löschen" name="submit"></p>
    </form>
</x-main-layout>
