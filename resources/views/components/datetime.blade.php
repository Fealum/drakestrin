@if ($isCurrent)
<strong>
@endif
{{ $dateString }}{{ $onlyDate ? '' : ',' }}
@if ($isCurrent)
</strong>
@endif
@unless ($onlyDate)
{{ $timeString }}
@endunless