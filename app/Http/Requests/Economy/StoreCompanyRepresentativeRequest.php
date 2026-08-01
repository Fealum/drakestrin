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
        $role = CompanyRepresentativeRole::tryFrom((string) $this->input('role'));

        return $company instanceof Company && $role && ($this->user()?->can(
            $role === CompanyRepresentativeRole::MANAGER ? 'manageManagers' : 'manageSiteRepresentatives',
            $company,
        ) ?? false);
    }

    public function rules(): array
    {
        $role = CompanyRepresentativeRole::tryFrom((string) $this->input('role'));
        $representativeUnique = Rule::unique('company_representatives', 'character_id')
            ->where('company_id', $this->route('company')->id);

        if ($role?->isSiteSpecific()) {
            $representativeUnique->where('company_site_id', (int) $this->input('company_site_id'));
        }

        return [
            'character_id' => [
                'required',
                'integer',
                'exists:characters,id',
                $representativeUnique,
                Rule::unique('company_owners', 'character_id')
                    ->where('company_id', $this->route('company')->id),
            ],
            'role' => ['required', Rule::enum(CompanyRepresentativeRole::class)],
            'company_site_id' => [
                'nullable',
                'integer',
                Rule::exists('company_sites', 'id')->where('company_id', $this->route('company')->id),
            ],
        ];
    }

    public function after(): array
    {
        return [function ($validator): void {
            $role = CompanyRepresentativeRole::tryFrom((string) $this->input('role'));

            if ($role?->isSiteSpecific() && ! $this->filled('company_site_id')) {
                $validator->errors()->add('company_site_id', 'Bitte wähle einen Standort aus.');
            }

            if ($role === CompanyRepresentativeRole::MANAGER && $this->filled('company_site_id')) {
                $validator->errors()->add('company_site_id', 'Die Geschäftsführung gilt für den gesamten Betrieb.');
            }
        }];
    }
}
