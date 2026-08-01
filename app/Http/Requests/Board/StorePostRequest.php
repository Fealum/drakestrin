<?php

namespace App\Http\Requests\Board;

use App\Data\Board\CreatePostData;
use App\Models\Board\Post;
use App\Support\PostTransferAction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'transfer_action' => ['nullable', Rule::enum(PostTransferAction::class), 'required_with:inventory'],
            'inventory' => ['nullable', 'array', 'min:1', 'required_with:transfer_action'],
            'inventory.*' => ['integer', 'exists:inventories,id'],
            'inventorystack' => ['nullable', 'array'],
            'inventorystack.*' => ['nullable', 'string', 'max:40'],
            'recipient' => [
                'nullable',
                'integer',
                'exists:characters,id',
                Rule::requiredIf(in_array($this->input('transfer_action'), [
                    PostTransferAction::GIVE->value,
                    PostTransferAction::COMPANY_WITHDRAWAL->value,
                ], true)),
            ],
            'company_site' => [
                'nullable',
                'integer',
                'exists:company_sites,id',
                Rule::requiredIf(in_array($this->input('transfer_action'), [
                    PostTransferAction::COMPANY_DEPOSIT->value,
                    PostTransferAction::COMPANY_WITHDRAWAL->value,
                ], true)),
            ],
        ];
    }

    public function toData(): CreatePostData
    {
        return CreatePostData::fromArray($this->validated());
    }
}
