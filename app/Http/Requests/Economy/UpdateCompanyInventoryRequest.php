<?php

namespace App\Http\Requests\Economy;

use App\Models\Economy\Company;
use App\Models\Economy\Inventory;
use App\Support\PermissionEntityType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->route('company');
        $inventory = $this->route('inventory');

        return $company instanceof Company
            && $inventory instanceof Inventory
            && (int) $inventory->owner_type === PermissionEntityType::COMPANY->value
            && (int) $inventory->owner_id === (int) $company->id
            && ($this->user()?->can('manage', $company) ?? false);
    }

    public function rules(): array
    {
        return [
            'state' => ['required', Rule::in(['production', 'reserved', 'sale'])],
            'price' => [
                'nullable',
                Rule::requiredIf($this->input('state') === 'sale'),
                'regex:/^\d+(?:[\.,]\d{1,4})?$/',
            ],
            'quantity' => ['nullable', 'string', 'max:40'],
        ];
    }

    public function targetWear(): int
    {
        return match ($this->validated('state')) {
            'production' => -2,
            'reserved' => -1,
            'sale' => $this->priceInSmallestUnit((string) $this->validated('price')),
        };
    }

    private function priceInSmallestUnit(string $price): int
    {
        $normalized = str_replace(',', '.', $price);
        [$whole, $fraction] = array_pad(explode('.', $normalized, 2), 2, '');

        return ((int) $whole * 10000) + (int) str_pad(substr($fraction, 0, 4), 4, '0');
    }
}
