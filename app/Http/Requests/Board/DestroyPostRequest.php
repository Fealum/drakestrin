<?php

namespace App\Http\Requests\Board;

use Illuminate\Foundation\Http\FormRequest;

class DestroyPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('delete', $this->route('post')) ?? false;
    }

    public function rules(): array
    {
        return [
            'delete' => ['required', 'accepted'],
        ];
    }
}
