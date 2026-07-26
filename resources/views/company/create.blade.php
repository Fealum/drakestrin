<x-main-layout title="Betrieb gründen" css="company_view">
    <form action="{{ route('company.store') }}" method="post">
        @csrf
        @include('company._form', [
            'company' => new \App\Models\Economy\Company(),
            'submitLabel' => 'Betrieb gründen',
        ])
    </form>
</x-main-layout>
