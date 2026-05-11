@inject('forumFormatter', 'App\Services\Board\ForumFormatter')

<x-main-layout :title="'Thema »'.$thread->name.'« löschen'" alt-title="Thema löschen" css="thread">
    <h3>Sicher?</h3>
    <p>Bist Du Dir sicher, dass Du dieses Thema mit allen Beiträgen löschen möchtest?</p>
    <p>Beim Löschen dieses Themas {{ $postCount === 1 ? 'wird 1 Beitrag' : 'werden '.number_format($postCount, 0, ',', '.').' Beiträge' }} gelöscht.</p>

    @if ($thread->firstPost)
    <div class="post">
        @if ($thread->firstPost->characterModel)
        <x-avatar :subject="$thread->firstPost->characterModel" size="list" />
        @endif
        <div class="postuser">
            <h4>
                @if ($thread->firstPost->characterModel)
                <a href="{{ url('/user/character/'.$thread->firstPost->characterModel->id) }}">{{ $thread->firstPost->characterModel->name }}</a>
                @else
                Unbekannter Charakter
                @endif
                <span class="datetime"><x-datetime :time="$thread->firstPost->time" /></span>
            </h4>
        </div>
        <div class="postcontent">{!! $forumFormatter->render($thread->firstPost->message, $thread->firstPost->smilies) !!}</div>
    </div>
    @endif

    <form name="deletethread" action="{{ route('thread.destroy', ['thread' => $thread->id]) }}" method="post">
        @csrf
        <input type="hidden" name="delete" value="1">
        <p><input type="submit" value="Thema löschen" name="submit"></p>
    </form>
</x-main-layout>
