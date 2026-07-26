@if ($paid > 0)
{{ $paid }} Arbeiter mit insgesamt {{ number_format($sumpaid / 10000, 2, ',', '.') }} Tuk ausgezahlt ({{ $months }} Monatslöhne).
@else
Mit dem vorhandenen Geld konnte kein fälliger Monatslohn ausgezahlt werden.
@endif
@if ($unpaid > 0)
    {{ $unpaid }} Arbeiter haben weiterhin ausstehenden Lohn.
@endif
