<x-main-layout title="IP-Adresse" css="thread">
    <x-slot:js>
        <script defer src="{{ asset('js/alpine.min.js') }}"></script>
        <script>
            window.sortableIpTable = function (initialRows, initialSort) {
                return {
                    rows: initialRows,
                    sortKey: initialSort,
                    direction: 'desc',
                    sortedRows() {
                        return [...this.rows].sort((left, right) => {
                            const leftValue = left[this.sortKey];
                            const rightValue = right[this.sortKey];

                            if (leftValue === rightValue) {
                                return 0;
                            }

                            const result = leftValue > rightValue ? 1 : -1;

                            return this.direction === 'asc' ? result : -result;
                        });
                    },
                    sort(key) {
                        if (this.sortKey === key) {
                            this.direction = this.direction === 'asc' ? 'desc' : 'asc';
                            return;
                        }

                        this.sortKey = key;
                        this.direction = key === 'label' || key === 'ip' ? 'asc' : 'desc';
                    },
                    marker(key) {
                        if (this.sortKey !== key) {
                            return '';
                        }

                        return this.direction === 'asc' ? ' ▲' : ' ▼';
                    },
                };
            };
        </script>
    </x-slot:js>

    @php
        $authorIpRows = $authorIps->map(fn ($ip) => [
            'ip' => $ip->ip,
            'post_count' => (int) $ip->post_count,
            'first_post_time' => (int) $ip->first_post_time,
            'first_post_time_formatted' => \Illuminate\Support\Carbon::createFromTimestamp((int) $ip->first_post_time)->format('d.m.Y, H:i'),
            'last_post_time' => (int) $ip->last_post_time,
            'last_post_time_formatted' => \Illuminate\Support\Carbon::createFromTimestamp((int) $ip->last_post_time)->format('d.m.Y, H:i'),
        ])->values();
        $sameIpUserRows = $sameIpUsers->map(fn ($entry) => [
            'label' => $entry->author?->name ?? ('Nutzer #'.$entry->user),
            'url' => $entry->author ? url('/user/view/'.$entry->author->id) : null,
            'is_post_author' => (int) $entry->user === $post->user,
            'post_count' => (int) $entry->post_count,
            'first_post_time' => (int) $entry->first_post_time,
            'first_post_time_formatted' => \Illuminate\Support\Carbon::createFromTimestamp((int) $entry->first_post_time)->format('d.m.Y, H:i'),
            'last_post_time' => (int) $entry->last_post_time,
            'last_post_time_formatted' => \Illuminate\Support\Carbon::createFromTimestamp((int) $entry->last_post_time)->format('d.m.Y, H:i'),
        ])->values();
    @endphp

    <div class="post">
        @if ($post->characterModel)
        <img src="{{ url('/img/character_avatar.id/thumb/'.$post->characterModel->avatarThumbPath().'.jpg') }}" alt="">
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
            <p>
                <a class="postnumber small" href="{{ url('/thread/view/'.$post->thread.'#post'.$post->id) }}">Beitrag #{{ $post->id }}</a>
            </p>
        </div>

        <div class="postcontent">
            <h3>IP-Adresse dieses Beitrags</h3>
            <p>{{ $post->ip ?: 'Keine IP-Adresse gespeichert.' }}</p>

            <h3>Weitere IP-Adressen dieses Nutzers</h3>
            @if ($authorIps->isNotEmpty())
            <table class="ip-table" x-data="sortableIpTable(@js($authorIpRows), 'post_count')">
                <thead>
                    <tr>
                        <th><button type="button" x-on:click="sort('ip')">IP-Adresse<span x-text="marker('ip')"></span></button></th>
                        <th><button type="button" x-on:click="sort('post_count')">Anzahl der Beiträge<span x-text="marker('post_count')"></span></button></th>
                        <th><button type="button" x-on:click="sort('first_post_time')">zuerst verwendet<span x-text="marker('first_post_time')"></span></button></th>
                        <th><button type="button" x-on:click="sort('last_post_time')">zuletzt verwendet<span x-text="marker('last_post_time')"></span></button></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="row in sortedRows()" :key="row.ip">
                        <tr>
                            <td x-text="row.ip"></td>
                            <td x-text="row.post_count.toLocaleString('de-DE')"></td>
                            <td x-text="row.first_post_time_formatted"></td>
                            <td x-text="row.last_post_time_formatted"></td>
                        </tr>
                    </template>
                    @foreach ($authorIps as $ip)
                    <tr class="no-js-row">
                        <td>{{ $ip->ip }}</td>
                        <td>{{ number_format($ip->post_count, 0, ',', '.') }}</td>
                        <td><x-datetime :time="\Illuminate\Support\Carbon::createFromTimestamp((int) $ip->first_post_time)" /></td>
                        <td><x-datetime :time="\Illuminate\Support\Carbon::createFromTimestamp((int) $ip->last_post_time)" /></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p>Keine weiteren IP-Adressen gefunden.</p>
            @endif

            <h3>Nutzer mit derselben IP-Adresse</h3>
            @if ($sameIpUsers->isNotEmpty())
            <table class="ip-table" x-data="sortableIpTable(@js($sameIpUserRows), 'post_count')">
                <thead>
                    <tr>
                        <th><button type="button" x-on:click="sort('label')">Nutzer<span x-text="marker('label')"></span></button></th>
                        <th><button type="button" x-on:click="sort('post_count')">Anzahl der Beiträge<span x-text="marker('post_count')"></span></button></th>
                        <th><button type="button" x-on:click="sort('first_post_time')">zuerst verwendet<span x-text="marker('first_post_time')"></span></button></th>
                        <th><button type="button" x-on:click="sort('last_post_time')">zuletzt verwendet<span x-text="marker('last_post_time')"></span></button></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="row in sortedRows()" :key="row.label">
                        <tr>
                            <td>
                                <template x-if="row.url">
                                    <a :href="row.url" x-text="row.label"></a>
                                </template>
                                <template x-if="! row.url">
                                    <span x-text="row.label"></span>
                                </template>
                                <span x-show="row.is_post_author"> (Autor dieses Beitrags)</span>
                            </td>
                            <td x-text="row.post_count.toLocaleString('de-DE')"></td>
                            <td x-text="row.first_post_time_formatted"></td>
                            <td x-text="row.last_post_time_formatted"></td>
                        </tr>
                    </template>
                    @foreach ($sameIpUsers as $entry)
                    <tr class="no-js-row">
                        <td>
                            @if ($entry->author)
                            <a href="{{ url('/user/view/'.$entry->author->id) }}">{{ $entry->author->name }}</a>
                            @else
                            Nutzer #{{ $entry->user }}
                            @endif
                            @if ((int) $entry->user === $post->user)
                            (Autor dieses Beitrags)
                            @endif
                        </td>
                        <td>{{ number_format($entry->post_count, 0, ',', '.') }}</td>
                        <td><x-datetime :time="\Illuminate\Support\Carbon::createFromTimestamp((int) $entry->first_post_time)" /></td>
                        <td><x-datetime :time="\Illuminate\Support\Carbon::createFromTimestamp((int) $entry->last_post_time)" /></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p>Keine Nutzer mit derselben IP-Adresse gefunden.</p>
            @endif
        </div>
    </div>
</x-main-layout>
