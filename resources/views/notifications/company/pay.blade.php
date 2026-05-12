{{ $paid }} Arbeiter mit insgesamt {{ number_format($sumpaid / 10000, 2, ',', '.') }} Tuk ausgezahlt.
@if ($unpaid > 0)
    {{ $unpaid }} Arbeiter konnten nicht ausgezahlt werden.
@endif
