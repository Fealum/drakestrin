@if($preview['type'] === 'transfer')
<div class="post-transfer composer-transfer-preview">
    <span class="composer-preview-party">@if($preview['source']['avatar'] ?? null)<img src="{{ $preview['source']['avatar'] }}" alt="">@endif{{ $preview['source']['label'] }}</span>
    <span class="fa fa-long-arrow-right" aria-hidden="true"></span><span class="sr-only">gibt</span>
    <span>@foreach($preview['items'] as $item){{ $item['name'] }} ({{ $item['quantity'] }})@unless($loop->last), @endunless @endforeach</span>
    <span class="fa fa-long-arrow-right" aria-hidden="true"></span><span class="sr-only">an</span>
    <span class="composer-preview-party">@if($preview['recipient']['avatar'] ?? null)<img src="{{ $preview['recipient']['avatar'] }}" alt="">@endif{{ $preview['recipient']['label'] }}</span>
</div>
@elseif($preview['type'] === 'poll')
<section class="post-poll">
    <x-poll-heading :question="$preview['element']['question']" :visibility="$preview['element']['visibility'] ?? 'anonymous'" :max-choices="$preview['element']['max_choices'] ?? 1" />
    <ol>@foreach($preview['options'] as $option)<li>{{ $option }}</li>@endforeach</ol>
    @if($preview['element']['closes_at'] ?? null)<p><small>Abstimmungsschluss: <x-datetime :time="\Illuminate\Support\Carbon::createFromTimestamp($preview['element']['closes_at'])" /></small></p>@endif
</section>
@elseif($preview['type'] === 'scene_transition')
    <x-scene-marker :label="$preview['label']" :location="$preview['location']" :time="$preview['time']" />
@endif
