<x-main-layout title="Mitglieder" css="user">
    <x-slot:js>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('main ol.users ol').forEach(function (list) {
                    const children = Array.from(list.children);

                    if (children.length <= 7) {
                        return;
                    }

                    children.slice(6).forEach(function (child) {
                        child.hidden = true;
                    });

                    const more = document.createElement('li');
                    more.className = 'morecharacters fa-chevron-down';
                    more.addEventListener('click', function () {
                        children.slice(6).forEach(function (child) {
                            child.hidden = false;
                        });
                        more.remove();
                    });
                    list.appendChild(more);
                });
            });
        </script>
    </x-slot:js>

    <h3>Sortieren</h3>
    <p>Nach:
        <a href="{{ route('user.viewall', ['order' => 'name,a']) }}">Namen</a> <a href="{{ route('user.viewall', ['order' => 'name,d']) }}">&nbsp;&darr;&nbsp;</a>,
        <a href="{{ route('user.viewall', ['order' => 'regdate,a']) }}">Registrierungsdatum</a> <a href="{{ route('user.viewall', ['order' => 'regdate,d']) }}">&nbsp;&darr;&nbsp;</a>,
        <a href="{{ route('user.viewall', ['order' => 'lastvisit,a']) }}">letztem Besuch</a> <a href="{{ route('user.viewall', ['order' => 'lastvisit,d']) }}">&nbsp;&darr;&nbsp;</a>,
        <a href="{{ route('user.viewall', ['order' => 'post,a;name,a']) }}">Beiträgen</a> <a href="{{ route('user.viewall', ['order' => 'post,d;name,d']) }}">&nbsp;&darr;&nbsp;</a>,
        <a href="{{ route('user.viewall', ['order' => 'postsperday,a;name,a']) }}">Beiträgen pro Tag</a> <a href="{{ route('user.viewall', ['order' => 'postsperday,d;name,d']) }}">&nbsp;&darr;&nbsp;</a>.
    </p>

    <h3>Ergebnisse</h3>
    @include('board._pagination', ['paginator' => $users, 'baseUrl' => route('user.viewall', ['order' => $order])])
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
                <p class="small">Letzter Besuch: <x-datetime :time="$user->lastvisit" /></p>
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
    @include('board._pagination', ['paginator' => $users, 'baseUrl' => route('user.viewall', ['order' => $order])])
</x-main-layout>
