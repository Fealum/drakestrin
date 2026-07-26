<?php

namespace App\Http\Requests\Economy;

use App\Data\Economy\StartProductionData;
use App\Support\InventoryStockState;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignLabourRequest extends FormRequest
{
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
            'prodas_value' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function productionData(): StartProductionData
    {
        return StartProductionData::fromArray($this->validated());
    }
}
