<x-main-layout title="Kontor" css="company_view">
    <ol class="companies">
        @foreach ($companies as $company)
        <li><a href="{{ route('company.view', $company->id) }}">{{ $company->name }}</a></li>
        @endforeach
    </ol>
</x-main-layout>
