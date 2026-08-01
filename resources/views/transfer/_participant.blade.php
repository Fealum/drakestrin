@if ($participant instanceof \App\Models\User\Character)
<a href="{{ url('/user/character/'.$participant->id) }}"><x-avatar :subject="$participant" size="list" :title="$participant->name" /></a>
@elseif ($participant instanceof \App\Models\Territory\Location)
<a class="transfer-location" href="{{ route('location.view', ['location' => $participant->id]) }}">{{ $participant->name }}</a>
@elseif ($participant instanceof \App\Models\Economy\Company)
<a href="{{ route('company.view', ['company' => $participant->id]) }}">{{ $participant->name }}</a>
@elseif ($participant instanceof \App\Models\Economy\CompanySite)
@php($showSite = $participant->company?->sites?->count() > 1)
<a href="{{ route('company.view', ['company' => $participant->company_id]).($showSite ? '#site-'.$participant->id : '') }}">{{ $participant->company?->name ?? 'Betrieb' }}@if($showSite) ({{ $participant->name }})@endif</a>
@elseif ($participant)
{{ $participant->name ?? class_basename($participant) }}
@else
Unbekannt
@endif
