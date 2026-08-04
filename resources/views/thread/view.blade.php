@inject('forumFormatter', 'App\Services\Board\ForumFormatter')

<x-main-layout :title="$thread->name" css="thread">
    <div>
        {{ number_format($thread->views, 0, ',', '.') }} Aufrufe,
        {{ number_format($thread->post_count, 0, ',', '.') }} Beiträge.
        @if ($canEditThread)
        <a class="option edit" title="editieren" href="{{ route('thread.edit', $thread) }}">editieren</a>
        @endif
        @if ($canDeleteThread)
        <a class="option delete" title="löschen" href="{{ route('thread.delete', $thread) }}">löschen</a>
        @endif
        @auth
            @if ($subscription)
            <form method="post" action="{{ route('thread.unsubscribe', $thread) }}" class="inline-form">
                @csrf
                @method('delete')
                <button type="submit" class="option unsubscribe" title="Abonnement beenden">Abonnement beenden</button>
            </form>
            @else
            <form method="post" action="{{ route('thread.subscribe', $thread) }}" class="inline-form">
                @csrf
                <button type="submit" class="option subscribe" title="abonnieren">Abonnieren</button>
            </form>
            @endif
        @endauth
        @if ($canViewSubscribers)
        <a class="option subscribe" href="{{ route('thread.subscribers', $thread) }}">Abonnenten ({{ $subscriberCount }})</a>
        @endif
    </div>

    @include('board._pagination', ['paginator' => $posts, 'baseUrl' => route('thread.view', $thread)])

    @forelse ($posts as $postNumber => $post)
    @php($character = $post->character)
    <div id="post{{ $post->id }}" class="post">
        @if ($character)
        <x-avatar :subject="$character" size="post" />
        @endif

        <div class="postuser">
            <h4>
                @if ($unreadPostIds->contains($post->id))
                <span class="option new">(Neu)</span>
                @endif
                @if ($character)
                <a href="{{ route('user.character', $character) }}">{{ $character->name }}</a>
                @else
                Unbekannter Charakter
                @endif
                <span class="datetime"><x-datetime :time="$post->time" /></span>
            </h4>
            <p>
                <a class="postnumber small" href="{{ route('thread.view', ['thread' => $thread, 'page' => $posts->currentPage() > 1 ? $posts->currentPage() : null]) }}#post{{ $post->id }}">{{ $postNumber + 1 + (($posts->currentPage() - 1) * $posts->perPage()) }}</a>
                @if($composerData)
                <button type="submit" form="post-composer" name="intent" value="quote:{{ $post->id }}" class="option quote" title="zitieren">zitieren</button>
                @endif
                @can('update', $post)
                <a class="option edit" title="editieren" href="{{ route('post.edit', $post) }}">editieren</a>
                @endcan
                @can('delete', $post)
                <a class="option delete" title="{{ $post->hasDurableElements() ? 'Inhalt löschen' : 'löschen' }}" href="{{ route('post.delete', $post) }}">{{ $post->hasDurableElements() ? 'Inhalt löschen' : 'löschen' }}</a>
                @endcan
                <a class="option report" title="melden" href="#">melden</a>
                @can('viewIp', $post)
                <a class="option ip" title="IP" href="{{ route('post.ip', $post) }}">IP</a>
                @endcan
            </p>
        </div>

        <div class="postcontent">
            @foreach($post->elements as $element)
                @if($element->type === \App\Support\PostElementType::MESSAGE && $element->message)
                    @include('post-element.message', ['message' => $element->message])
                @elseif($element->type === \App\Support\PostElementType::TRANSFER)
                    @include('post-element.transfer', ['element' => $element])
                @elseif($element->type === \App\Support\PostElementType::SCENE_TRANSITION)
                    @include('post-element.scene-transition', ['element' => $element])
                @elseif($element->type === \App\Support\PostElementType::POLL)
                    @include('post-element.poll', ['element' => $element])
                @endif
            @endforeach
            @if($post->elements->isEmpty() && trim((string) $post->message) !== '')
                {!! $forumFormatter->render($post->message, $post->smilies) !!}
            @endif
        </div>
    </div>
    @empty
    <p>Keine Beiträge!</p>
    @endforelse

    @include('board._pagination', ['paginator' => $posts, 'baseUrl' => route('thread.view', $thread)])

    @if ($composerData)
        @include('draft.edit', $composerData)
    @endif
</x-main-layout>
