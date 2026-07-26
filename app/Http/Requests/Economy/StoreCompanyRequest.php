<?php

namespace App\Http\Requests\Economy;

use App\Data\Economy\CompanyData;
use App\Models\Economy\Company;
use App\Support\CompanySector;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Company::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sector' => ['required', Rule::enum(CompanySector::class)],
            'owner_character_id' => [
                'required',
                'integer',
                Rule::exists('characters', 'id')->where('user_id', $this->user()->id),
            ],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'text' => ['nullable', 'string'],
            'url' => ['nullable', 'url', 'max:2048'],
            'is_storefront' => ['nullable', 'boolean'],
        ];
    }

    public function toData(): CompanyData
    {
        return CompanyData::fromArray($this->validated());
    }
}
