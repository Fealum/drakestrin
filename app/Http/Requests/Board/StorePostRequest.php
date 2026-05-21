<?php

namespace App\Http\Requests\Board;

use App\Data\Board\CreatePostData;
use App\Models\Board\Post;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [Post::class, $this->route('thread')]) ?? false;
    }

    public function rules(): array
    {
        return [
            'character' => ['required'],
            'message' => ['nullable', 'string', 'required_without:inventory'],
            'newcharname' => ['nullable', 'string', 'max:85'],
            'smilies' => ['nullable', 'boolean'],
            'signature' => ['nullable', 'boolean'],
            'inventory' => ['nullable', 'array'],
            'inventory.*' => ['integer', 'exists:inventories,id'],
            'inventorystack' => ['nullable', 'array'],
            'inventorystack.*' => ['nullable', 'string', 'max:40'],
            'recipient' => ['nullable', 'integer', 'exists:characters,id', 'required_with:inventory'],
        ];
    }

    public function toData(): CreatePostData
    {
        return CreatePostData::fromArray($this->validated());
    }
}
