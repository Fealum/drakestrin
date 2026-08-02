<x-main-layout :title="'Gruppe »'.$group->name.'«'" css="group_view">
    @if ($users->count() > 0)
    <h3>Mitglieder</h3>
    @include('board._pagination', ['paginator' => $users, 'baseUrl' => route('group.view', $group)])
    <ol class="users">
        @foreach ($users as $user)
        <li>
            <div>
                <p>
                    <a href="{{ route('user.view', $user) }}">
                        <x-avatar :subject="$user" size="post" />
                        {{ $user->name }}
                    </a>
                </p>
                <p class="small">
                    <a href="{{ route('board.filter', ['filter' => 'user_contains:'.$user->id]) }}">{{ number_format($user->post_count ?? 0, 0, ',', '.') }} Beiträge</a>
                    seit <x-datetime :time="$user->regdate" only-date="1" />
                    ({{ number_format($user->postsPerDay(), 2, ',', '.') }} / Tag)
                </p>
            </div>
            @if ($user->characters->isNotEmpty())
            <ol>
                @foreach ($user->characters as $character)
                    @include('user._character-list-item', ['character' => $character])
                @endforeach
            </ol>
            @endif
        </li>
        @endforeach
    </ol>
    @include('board._pagination', ['paginator' => $users, 'baseUrl' => route('group.view', $group)])
    @endif
</x-main-layout>
