<x-main-layout title="Kontor" css="company_view">
    @if ($canCreate)
    <p><a href="{{ route('company.create') }}">Betrieb gründen</a></p>
    @endif
    <ol class="companies">
        @foreach ($companies as $company)
        <li><a href="{{ route('company.view', $company->id) }}">{{ $company->name }}</a></li>
        @endforeach
    </ol>
</x-main-layout>
