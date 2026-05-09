<?php

namespace App\Services\Dictionary;

use App\Models\Dictionary\Word;
use Illuminate\Support\Collection;

class DictionarySearchService
{
    public function search(string $query, ?int $excludeWordId = null): Collection
    {
        return Word::with(['languageModel', 'wordType'])
            ->where(function ($builder) use ($query) {
                $builder->where('word', 'like', '%' . $query . '%');

                if (ctype_digit($query)) {
                    $builder->orWhere('id', (int)$query);
                }
            })
            ->when($excludeWordId, fn ($builder) => $builder->where('id', '!=', $excludeWordId))
            ->orderByRaw('LENGTH(word) ASC')
            ->limit(10)
            ->get();
    }

    public function toAutocompletePayload(Collection $words): Collection
    {
        return $words->map(fn (Word $word) => [
            'id' => $word->id,
            'word' => $word->word,
            'wordtype' => $word->wordType?->code,
            'wordtype_name' => $word->wordType?->name,
            'language' => $word->getAttribute('language'),
            'language_code' => $word->languageModel?->code,
            'language_name' => $word->languageModel?->name,
        ]);
    }
}
