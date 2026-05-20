<?php

namespace App\Http\Requests\Board;

use App\Data\Board\CreateThreadData;
use App\Models\Board\Thread as ForumThread;
use Illuminate\Foundation\Http\FormRequest;

class StoreThreadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', ForumThread::class) ?? false;
    }

    public function rules(): array
    {
        if (! $this->isMethod('post')) {
            return [];
        }

        return [
            'board' => ['required', 'integer', 'exists:boards,id'],
            'character' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:225'],
            'message' => ['required', 'string'],
            'important' => ['nullable', 'boolean'],
            'smilies' => ['nullable', 'boolean'],
            'signature' => ['nullable', 'boolean'],
        ];
    }

    public function toData(): CreateThreadData
    {
        return CreateThreadData::fromArray($this->validated());
    }
}
