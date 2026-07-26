<x-main-layout :title="$company->name.' bearbeiten'" css="company_view">
    <form action="{{ route('company.update', ['company' => $company->id]) }}" method="post">
        @csrf
        @method('put')
        @include('company._form', [
            'submitLabel' => 'Änderungen speichern',
        ])
    </form>
</x-main-layout>
