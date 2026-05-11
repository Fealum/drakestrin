<x-main-layout :title="$user->name" css="user_id">
    <x-slot:js>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const list = document.querySelector('main ol#user_characters');

                if (!list) {
                    return;
                }

                const children = Array.from(list.querySelectorAll(':scope > li:not(#createchar)'));

                if (children.length <= 7) {
                    return;
                }

                children.slice(6).forEach(function (child) {
                    child.hidden = true;
                });

                const more = document.createElement('li');
                more.id = 'morecharacters';
                more.className = 'fa-chevron-down';
                more.addEventListener('click', function () {
                    children.slice(6).forEach(function (child) {
                        child.hidden = false;
                    });
                    more.remove();
                });
                list.appendChild(more);
            });
        </script>
    </x-slot:js>

    <x-avatar :subject="$user" size="full" />
    @if ($user->usertext)
    <p id="usertext" @class(['usertext_long' => mb_strlen($user->usertext) > 80])>{!! nl2br(e($user->usertext)) !!}</p>
    @endif

    <ol class="columns">
        <li><h4>Aktivität</h4>
            <ol>
                <li><em>Mitglied seit:</em> <x-datetime :time="$user->regdate" /></li>
                <li><em>Letzter Besuch:</em> <x-datetime :time="$user->lastvisit" /></li>
                <li><em>Beiträge insgesamt:</em> <a href="{{ url('/board/filter/user_contains:'.$user->id) }}">{{ number_format($user->post__total ?? 0, 0, ',', '.') }}</a></li>
                <li><em>Beiträge pro Tag:</em> {{ number_format($user->postsPerDay(), 2, ',', '.') }}</li>
            </ol>
        </li>
        @if ($user->groups->isNotEmpty())
        <li><h4>Gruppen</h4>
            <ol>
                @foreach ($user->groups as $group)
                <li><a href="{{ route('group.view', $group->id) }}">{{ $group->name }}</a></li>
                @endforeach
            </ol>
        </li>
        @endif
        <li><h4>Charaktere</h4>
            @if ($user->characters->isNotEmpty() || $canCreateCharacter)
            <ol id="user_characters">
                @foreach ($user->characters as $character)
                    @include('user._character-list-item', ['character' => $character])
                @endforeach
                @if ($canCreateCharacter)
                <li id="createchar">
                    <form name="createcharacter" action="{{ url('/user/createcharacter/'.$user->id) }}" method="post">
                        <p class="small"><img src="{{ asset('css/img/newchar.png') }}" alt=""><input type="text" name="name" maxlength="85" placeholder="Neuer Charakter" required><input type="submit" value="erstellen" name="submit"></p>
                        <p class="small">&nbsp;</p>
                    </form>
                </li>
                @endif
            </ol>
            @endif
        </li>
        <li><h4>Informationen
            @if ($canEdit)
            <a href="{{ route('user.edit', $user->id) }}" class="option edit" title="editieren">editieren</a>
            @endif
        </h4>
            <ol>
                @if ((int) $user->gender > 0)
                <li><em>Geschlecht:</em> {{ match ((int) $user->gender) { 1 => 'männlich', 2 => 'weiblich', default => 'anderes' } }}</li>
                @endif
                @if ((int) $user->birthday > 0)
                <li><em>Geburtstag:</em> {{ $user->birthday }}</li>
                @endif
                @if ($user->location)
                <li><em>Herkunft:</em> {{ $user->location }}</li>
                @endif
                @if ($user->interests)
                <li><em>Interessen:</em> {{ $user->interests }}</li>
                @endif
                @if ($user->work)
                <li><em>Amt oder Beruf:</em> {{ $user->work }}</li>
                @endif
            </ol>
        </li>
        @if ($user->contacts->isNotEmpty() || $canEdit || auth()->check())
        <li><h4>Kontakt
            @if ($canEdit)
            <a href="{{ route('user.create_contact', $user->id) }}" class="option create" title="neue Kontaktmöglichkeit hinzufügen">neue Kontaktmöglichkeit hinzufügen</a>
            @endif
        </h4>
            @if ($user->contacts->isNotEmpty() || auth()->check())
            <ol>
                @auth
                    @if (auth()->id() !== $user->id)
                    <li><a href="{{ route('conversation.view', $user->id) }}">Private Konversation</a></li>
                    @endif
                @endauth
                @foreach ($user->contacts as $contact)
                @php($protocol = $contact->protocolModel)
                <li><em>{{ $protocol?->name }}:</em>
                    @if ($protocol?->link)
                    <a href="{{ str_replace('\\1', $contact->contact, $protocol->link) }}">{{ $contact->contact }}</a>
                    @else
                    {{ $contact->contact }}
                    @endif
                    @if ($canEdit)
                    <a href="{{ route('user.edit_contact', $contact->id) }}" class="option edit" title="Kontaktmöglichkeit bearbeiten">Kontaktmöglichkeit bearbeiten</a>
                    <a href="{{ route('user.delete_contact', $contact->id) }}" class="option delete" title="Kontaktmöglichkeit löschen">Kontaktmöglichkeit löschen</a>
                    @endif
                </li>
                @endforeach
            </ol>
            @endif
        </li>
        @endif
    </ol>
</x-main-layout>
