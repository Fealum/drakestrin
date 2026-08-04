<x-main-layout title="Entwürfe" css="thread">
    <p><a class="option new" href="{{ route('draft.topic') }}">Neues Thema entwerfen</a></p>
    @forelse ($drafts as $draft)
    <article class="draft-list-item">
        <h3><a href="{{ $draft->thread ? route('thread.view', $draft->thread).'#post-composer' : route('draft.edit', $draft) }}">{{ $draft->thread?->name ?? $draft->title ?? 'Neues Thema' }}</a></h3>
        <p>{{ $draft->thread ? 'Antwort' : 'Neues Thema' }} · zuletzt gespeichert <x-datetime :time="$draft->updated_at" /></p>
        <form method="post" action="{{ route('draft.destroy', $draft) }}">@csrf @method('delete')<button type="submit">Entwurf löschen</button></form>
    </article>
    @empty
    <p>Keine gespeicherten Entwürfe.</p>
    @endforelse
</x-main-layout>
