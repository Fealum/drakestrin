Arbeiter {{ $workername }} erfolgreich entlassen.
@if ($owed > 0)
    @if ($paid > 0)
        Es wurden {{ \App\Support\Currency::format($paid) }} ausbezahlt.
    @endif
    @if ($unpaid > 0)
        {{ \App\Support\Currency::format($unpaid) }} konnten nicht mehr ausbezahlt werden.
    @endif
@endif
