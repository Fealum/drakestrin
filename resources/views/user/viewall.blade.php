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
        <a href="{{ url('/user/viewall/name,a') }}">Namen</a> <a href="{{ url('/user/viewall/name,d') }}">&nbsp;&darr;&nbsp;</a>,
        <a href="{{ url('/user/viewall/regdate,a') }}">Registrierungsdatum</a> <a href="{{ url('/user/viewall/regdate,d') }}">&nbsp;&darr;&nbsp;</a>,
        <a href="{{ url('/user/viewall/lastvisit,a') }}">letztem Besuch</a> <a href="{{ url('/user/viewall/lastvisit,d') }}">&nbsp;&darr;&nbsp;</a>,
        <a href="{{ url('/user/viewall/post,a;name,a') }}">Beiträgen</a> <a href="{{ url('/user/viewall/post,d;name,d') }}">&nbsp;&darr;&nbsp;</a>,
        <a href="{{ url('/user/viewall/postsperday,a;name,a') }}">Beiträgen pro Tag</a> <a href="{{ url('/user/viewall/postsperday,d;name,d') }}">&nbsp;&darr;&nbsp;</a>.
    </p>

    <h3>Ergebnisse</h3>
    @include('board._pagination', ['paginator' => $users, 'baseUrl' => url('/user/viewall/'.$order)])
    <ol class="users">
        @foreach ($users as $user)
        <li>
            <div>
                <p>
                    <a href="{{ route('user.view', $user->id) }}">
                        <x-avatar :subject="$user" size="post" />
                        {{ $user->name }}
                    </a>
                </p>
                <p class="small">
                    <a href="{{ url('/board/filter/user_contains:'.$user->id) }}">{{ number_format($user->post__total ?? 0, 0, ',', '.') }} Beiträge</a>
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
    @include('board._pagination', ['paginator' => $users, 'baseUrl' => url('/user/viewall/'.$order)])
</x-main-layout>
