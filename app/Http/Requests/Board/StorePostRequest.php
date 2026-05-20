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
            'message' => ['required', 'string'],
            'newcharname' => ['nullable', 'string', 'max:85'],
            'smilies' => ['nullable', 'boolean'],
            'signature' => ['nullable', 'boolean'],
        ];
    }

    public function toData(): CreatePostData
    {
        return CreatePostData::fromArray($this->validated());
    }
}
