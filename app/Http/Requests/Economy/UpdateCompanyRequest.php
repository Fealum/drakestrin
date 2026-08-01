<?php

namespace App\Http\Requests\Economy;

use App\Data\Economy\CompanyData;
use App\Models\Economy\Company;
use App\Support\CompanySector;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->route('company');

        return $company instanceof Company && ($this->user()?->can('update', $company) ?? false);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sector' => ['required', Rule::enum(CompanySector::class)],
            'description' => ['nullable', 'string', 'max:65535'],
        ];
    }

    public function toData(): CompanyData
    {
        return CompanyData::fromArray($this->validated());
    }
}
