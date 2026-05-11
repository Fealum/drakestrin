<x-main-layout :title="$character->name" css="user_id">
    @if ($character->avatar)
    <x-avatar :subject="$character" size="full" />
    @endif
    @if ($character->usertext)
    <p id="usertext" @class(['usertext_long' => mb_strlen($character->usertext) > 80])>{!! nl2br(e($character->usertext)) !!}</p>
    @endif

    <ol class="columns">
        <li><h4>Aktivität</h4>
            <ol>
                <li><em>Charakter erstellt:</em> <x-datetime :time="$character->regdate" /></li>
                @if ($character->userModel)
                <li><em>Übergeordneter Nutzer:</em> <a href="{{ route('user.view', $character->userModel->id) }}">{{ $character->userModel->name }}</a></li>
                @endif
                <li><em>Beiträge insgesamt:</em> <a href="{{ url('/board/filter/char_contains:'.$character->id) }}">{{ number_format($character->post__total ?? 0, 0, ',', '.') }}</a></li>
                <li><em>Beiträge pro Tag:</em> {{ number_format($character->postsPerDay(), 2, ',', '.') }}</li>
            </ol>
        </li>
        <li><h4>Informationen
            @if ($canEdit)
            <a href="{{ route('user.edit_character', $character->id) }}" class="option edit" title="editieren">editieren</a>
            @endif
        </h4>
            <ol>
                @if ((int) $character->gender > 0)
                <li><em>Geschlecht:</em> {{ (int) $character->gender === 1 ? 'männlich' : 'weiblich' }}</li>
                @endif
                @if ((int) $character->birthday > 0)
                <li><em>Geburtstag:</em> {{ $character->birthday }}</li>
                @endif
                @if ($character->location)
                <li><em>Herkunft:</em> {{ $character->location }}</li>
                @endif
                @if ($character->interests)
                <li><em>Interessen:</em> {{ $character->interests }}</li>
                @endif
                @if ($character->work)
                <li><em>Amt oder Beruf:</em> {{ $character->work }}</li>
                @endif
            </ol>
        </li>
        @if ($character->territories->isNotEmpty())
        <li><h4>Lehen</h4>
            <ol>
                @foreach ($character->territories as $territory)
                <li class="territory_info">
                    <a href="{{ route('territory.view', $territory->id) }}">
                        <img src="{{ url('/img/territory.id/'.$territory->id.'.png') }}" alt="Wappen von {{ $territory->name }}">
                        {{ $territory->rulerTitle() }} von {{ $territory->name }}
                    </a>
                </li>
                @endforeach
            </ol>
        </li>
        @endif
        @if ($character->inventory->isNotEmpty())
        <li><h4>Besitz</h4>
            <ol class="inventory">
                @foreach ($character->inventory as $inventory)
                @php($item = $inventory->itemModel)
                @if ($item)
                <li>
                    <img src="{{ url('/img/item.img/'.$item->img.'.png') }}" title="{{ $item->name }}" alt="">
                    @if ($inventory->stack > 0)
                    <div>{{ $inventory->makeunitary() }}</div>
                    @endif
                </li>
                @endif
                @endforeach
            </ol>
        </li>
        @endif
    </ol>

    @if ($character->companies->isNotEmpty())
    <h3>Betriebe</h3>
    <ol>
        @foreach ($character->companies as $company)
        <li><a href="{{ url('/company/view/'.$company->id) }}">{{ $company->name }}</a>: {{ $company->description }}</li>
        @endforeach
    </ol>
    @endif
</x-main-layout>
