<?php

namespace App\Http\Requests\Economy;

use App\Models\Economy\Company;
use App\Support\CompanyRepresentativeRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRepresentativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->route('company');

        return $company instanceof Company && ($this->user()?->can('manageRepresentatives', $company) ?? false);
    }

    public function rules(): array
    {
        return [
            'character_id' => [
                'required',
                'integer',
                'exists:characters,id',
                Rule::notIn([(int) $this->route('company')->character_id]),
                Rule::unique('company_representatives', 'character_id')
                    ->where('company_id', $this->route('company')->id),
            ],
            'role' => ['required', Rule::enum(CompanyRepresentativeRole::class)],
        ];
    }
}
