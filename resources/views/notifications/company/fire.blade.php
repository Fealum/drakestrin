Arbeiter {{ $workername }} erfolgreich entlassen.
@if ($owed > 0)
    @if ($paid > 0)
        Es wurden {{ number_format($paid / 10000, 2, ',', '.') }} Tuk ausbezahlt.
    @endif
    @if ($unpaid > 0)
        {{ number_format($unpaid / 10000, 2, ',', '.') }} Tuk konnten nicht mehr ausbezahlt werden.
    @endif
@endif
