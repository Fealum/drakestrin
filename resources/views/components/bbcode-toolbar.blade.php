@props(['composer' => false, 'canTransfer' => false, 'canCreatePoll' => false, 'canSetScene' => false, 'canEndScene' => false])

<div @class(['bbcode-toolbar', 'composer-toolbar' => $composer]) role="group" aria-label="{{ $composer ? 'Text und Bausteine' : 'Textformatierung' }}" @if($composer) x-on:mousedown.prevent @endif>
    <button type="button" title="Fett" aria-label="Fett" x-on:click="insert('[b]', '[/b]', 'Text')"><strong>B</strong></button>
    <button type="button" title="Kursiv" aria-label="Kursiv" x-on:click="insert('[i]', '[/i]', 'Text')"><em>I</em></button>
    <button type="button" title="Unterstrichen" aria-label="Unterstrichen" x-on:click="insert('[u]', '[/u]', 'Text')"><u>U</u></button>
    <button type="button" title="Zitat" aria-label="Zitat" x-on:click="insert('[q]', '[/q]', 'Zitat')"><span class="fa fa-quote-right" aria-hidden="true"></span></button>
    <button type="button" title="Link" aria-label="Link" x-on:click="insert('[url=https://]', '[/url]', 'Linktext')"><span class="fa fa-link" aria-hidden="true"></span></button>
    <button type="button" title="Bild" aria-label="Bild" x-on:click="insert('[img]', '[/img]', 'https://')"><span class="fa fa-image" aria-hidden="true"></span></button>
    <button type="button" title="Liste" aria-label="Liste" x-on:click="insertListItem()"><span class="fa fa-list-ul" aria-hidden="true"></span></button>
    <button type="button" title="Handlungstext" aria-label="Handlungstext" x-on:click="insert('[h]', '[/h]', 'Handlung')">H</button>
    <button type="button" title="SimOff" aria-label="SimOff" x-on:click="insert('[s]', '[/s]', 'SimOff')">S</button>
    <span class="bbcode-smilies">
        <button type="button" title="Smileys" aria-label="Smileys" x-on:click="smiliesOpen = !smiliesOpen" x-bind:aria-expanded="smiliesOpen">:)</button>
        <span class="bbcode-smiley-picker" x-cloak x-show="smiliesOpen" x-on:click.outside="smiliesOpen = false">
            @foreach (\App\Services\Board\ForumFormatter::emoticons() as $code => [$file, $title])
            <button type="button" title="{{ $title ?: $code }}" aria-label="{{ $title ?: $code }}" x-on:click="insertSmiley(@js($code))"><img src="{{ asset('images/emoticon/'.$file) }}" alt="{{ $code }}"></button>
            @endforeach
        </span>
    </span>
    @if($composer)
    <span class="composer-marker-tools">
        @if($canTransfer)<button type="button" title="Übergabe einfügen" aria-label="Übergabe einfügen" x-bind:disabled="!markerAllowed('transfer') || busy" x-on:click="insertMarker('transfer')"><span class="fa fa-exchange" aria-hidden="true"></span></button>@endif
        @if($canCreatePoll)<button type="button" title="Umfrage einfügen" aria-label="Umfrage einfügen" x-bind:disabled="!markerAllowed('poll') || busy" x-on:click="insertMarker('poll')"><span class="fa fa-bar-chart" aria-hidden="true"></span></button>@endif
        @if($canSetScene || $canEndScene)<button type="button" title="Szene einfügen" aria-label="Szene einfügen" x-bind:disabled="!markerAllowed('scene') || busy" x-on:click="insertMarker('scene')"><span class="fa fa-map-marker" aria-hidden="true"></span></button>@endif
    </span>
    <span class="composer-toolbar-status" role="status"><span class="composer-spinner" x-show="busy" aria-hidden="true"></span><span class="sr-only" x-show="busy">Wird verarbeitet ...</span></span>
    @endif
</div>
