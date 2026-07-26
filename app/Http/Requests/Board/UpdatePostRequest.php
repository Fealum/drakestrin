<?php

namespace App\Http\Requests\Board;

use App\Data\Board\UpdatePostData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('post')) ?? false;
    }

    public function rules(): array
    {
        $post = $this->route('post');

        return [
            'character' => ['required', 'integer'],
            'message' => [
                'nullable',
                'string',
                Rule::requiredIf(! $post?->transfers()->exists()),
            ],
        ];
    }

    public function toData(): UpdatePostData
    {
        return UpdatePostData::fromArray($this->validated());
    }
}
