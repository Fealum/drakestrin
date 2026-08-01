<?php

namespace App\Http\Requests\Economy;

use App\Data\Economy\StartProductionData;
use App\Support\InventoryStockState;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignLabourRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $price = $this->input('prodas_value');

        if (! is_array($price)) {
            return;
        }

        foreach (['til', 'tuk', 'ten'] as $denomination) {
            if (($price[$denomination] ?? null) === null || $price[$denomination] === '') {
                $price[$denomination] = 0;
            }
        }

        $this->merge(['prodas_value' => $price]);
    }

    public function rules(): array
    {
        return [
            'labour' => ['required', 'integer', 'exists:labours,id'],
            'quantity' => ['required', 'integer', 'in:-1,0'],
            'quantity_count' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'instances' => ['required', 'integer', 'min:1', 'max:9999'],
            'prodas' => [
                'required',
                'integer',
                Rule::in([
                    InventoryStockState::PRODUCTION->value,
                    InventoryStockState::RESERVED->value,
                    0,
                ]),
            ],
            'prodas_value' => ['nullable', 'array:til,tuk,ten', 'required_if:prodas,0'],
            'prodas_value.til' => ['required_if:prodas,0', 'integer', 'min:0', 'max:1000000000'],
            'prodas_value.tuk' => ['required_if:prodas,0', 'integer', 'min:0', 'max:1000000000'],
            'prodas_value.ten' => ['required_if:prodas,0', 'integer', 'min:0', 'max:1000000000'],
        ];
    }

    public function productionData(): StartProductionData
    {
        return StartProductionData::fromArray($this->validated());
    }
}
