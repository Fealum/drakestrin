<?php

namespace App\Http\Requests\Board;

use App\Data\Board\UpdateThreadData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateThreadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('thread')) ?? false;
    }

    public function rules(): array
    {
        if (! $this->isMethod('post')) {
            return [];
        }

        return [
            'board' => ['required', 'integer', 'exists:boards,id'],
            'name' => ['required', 'string', 'max:225'],
            'important' => ['nullable', 'boolean'],
        ];
    }

    public function data(): UpdateThreadData
    {
        return UpdateThreadData::fromArray($this->validated());
    }
}
