<?php

namespace App\Http\Requests\Economy;

use App\Data\Economy\CompanyData;
use App\Models\Economy\Company;
use App\Models\Territory\Location;
use App\Models\Territory\Territory;
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
            'location_mode' => ['required', Rule::in(['existing', 'new'])],
            'location_id' => ['nullable', 'integer', 'exists:locations,id', 'required_if:location_mode,existing'],
            'fauthei_id' => ['nullable', 'integer', 'exists:territories,id', 'required_if:location_mode,new'],
            'description' => ['nullable', 'string', 'max:65535'],
        ];
    }

    public function after(): array
    {
        return [function ($validator): void {
            if ($this->input('location_mode') !== 'new' || ! $this->filled('fauthei_id')) {
                return;
            }

            $territory = Territory::query()->find((int) $this->input('fauthei_id'));

            if (! $territory || (string) $territory->type !== '5' || $territory->children()->exists()) {
                $validator->errors()->add('fauthei_id', 'Bitte wähle eine Fauthei aus.');

                return;
            }

            if (! $this->user()?->can('create', [Location::class, $territory])) {
                $validator->errors()->add('fauthei_id', 'An dieser Stelle darfst du keinen Ort erstellen.');
            }
        }];
    }

    public function toData(): CompanyData
    {
        return CompanyData::fromArray($this->validated());
    }
}
