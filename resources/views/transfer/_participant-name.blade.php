@if ($participant instanceof \App\Models\User\Character)
<a href="{{ route('user.character', $participant) }}">{{ $participant->name }}</a>
@elseif ($participant instanceof \App\Models\Territory\Location)
<a href="{{ route('location.view', $participant) }}">{{ $participant->name }}</a>
@elseif ($participant instanceof \App\Models\Economy\Company)
<a href="{{ route('company.view', $participant) }}">{{ $participant->name }}</a>
@elseif ($participant instanceof \App\Models\Economy\CompanySite)
@php($showSite = $participant->company?->sites?->count() > 1)
<a href="{{ route('company.view', $participant->company).($showSite ? '#site-'.$participant->id : '') }}">{{ $participant->company?->name ?? 'Betrieb' }}@if($showSite) ({{ $participant->name }})@endif</a>
@elseif ($participant)
{{ $participant->name ?? class_basename($participant) }}
@else
Unbekannt
@endif
