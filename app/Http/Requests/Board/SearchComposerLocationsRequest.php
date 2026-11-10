<?php

namespace App\Http\Requests\Board;

use Illuminate\Foundation\Http\FormRequest;

class SearchComposerLocationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'index' => ['required', 'integer', 'min:0', 'max:10000'],
            'selected' => ['nullable', 'integer', 'exists:locations,id'],
        ];
    }
}
