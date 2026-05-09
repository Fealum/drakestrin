<?php

namespace App\Services\Dictionary;

use App\Models\Dictionary\Key;
use App\Models\Dictionary\Word;
use App\Models\Dictionary\WordType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DictionaryWriter
{
    public function createWord(int $languageId, int $wordTypeId, string $word): Word
    {
        return Word::create([
            'language' => $languageId,
            'wordtype' => $wordTypeId,
            'word' => trim($word),
            'val' => 0,
        ]);
    }

    public function createAdvancedWords(array $validated): void
    {
        DB::transaction(function () use ($validated) {
            foreach (explode("\n", $validated['advanced']) as $line) {
                $this->createAdvancedLine($line, $validated);
            }
        });
    }

    public function createTranslationKeys(Word $sourceWord, Collection $targetIds, bool $bijective): void
    {
        $existingTargetIds = Word::whereIn('id', $targetIds)->pluck('id');

        foreach ($existingTargetIds as $targetId) {
            Key::firstOrCreate([
                'dictionary__from' => $sourceWord->id,
                'dictionary__to' => $targetId,
            ]);

            if ($bijective) {
                Key::firstOrCreate([
                    'dictionary__from' => $targetId,
                    'dictionary__to' => $sourceWord->id,
                ]);
            }
        }
    }

    public function deleteWordWithKeys(Word $word): void
    {
        DB::transaction(function () use ($word) {
            Key::where('dictionary__from', $word->id)
                ->orWhere('dictionary__to', $word->id)
                ->delete();

            $word->delete();
        });
    }

    private function createAdvancedLine(string $line, array $validated): void
    {
        $sourceWordId = null;

        foreach (explode('.', $line) as $index => $entry) {
            if (trim($entry) === '') {
                continue;
            }

            [$word, $wordTypeCode] = array_pad(explode(',', $entry, 2), 2, null);
            $wordType = WordType::firstWhere('code', trim((string)$wordTypeCode));

            if (!$wordType) {
                continue;
            }

            $newWord = $this->createWord(
                $index === 0 ? $validated['advanced-language-from'] : $validated['advanced-language-to'],
                $wordType->id,
                $word,
            );

            if ($index === 0) {
                $sourceWordId = $newWord->id;
            } elseif ($sourceWordId) {
                Key::create([
                    'dictionary__from' => $sourceWordId,
                    'dictionary__to' => $newWord->id,
                ]);
                Key::create([
                    'dictionary__from' => $newWord->id,
                    'dictionary__to' => $sourceWordId,
                ]);
            }
        }
    }
}
