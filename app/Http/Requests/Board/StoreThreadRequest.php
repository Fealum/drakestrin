<?php

namespace App\Http\Requests\Board;

use App\Data\Board\CreateThreadData;
use App\Models\Board\Thread as ForumThread;
use Carbon\CarbonImmutable;
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
            'scene_location' => ['nullable', 'integer', 'exists:locations,id'],
            'scene_story_started_at' => ['nullable', 'date_format:Y-m-d\TH:i'],
        ];
    }

    public function toData(): CreateThreadData
    {
        $data = $this->validated();

        if (filled($data['scene_story_started_at'] ?? null)) {
            $data['scene_story_started_at'] = CarbonImmutable::createFromFormat(
                'Y-m-d\TH:i',
                $data['scene_story_started_at'],
                config('app.timezone'),
            )->timestamp;
        }

        return CreateThreadData::fromArray($data);
    }
}
