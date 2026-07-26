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
            'language_id' => $languageId,
            'word_type_id' => $wordTypeId,
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
                'from_word_id' => $sourceWord->id,
                'to_word_id' => $targetId,
            ]);

            if ($bijective) {
                Key::firstOrCreate([
                    'from_word_id' => $targetId,
                    'to_word_id' => $sourceWord->id,
                ]);
            }
        }
    }

    public function deleteWordWithKeys(Word $word): void
    {
        DB::transaction(function () use ($word) {
            Key::where('from_word_id', $word->id)
                ->orWhere('to_word_id', $word->id)
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
                    'from_word_id' => $sourceWordId,
                    'to_word_id' => $newWord->id,
                ]);
                Key::create([
                    'from_word_id' => $newWord->id,
                    'to_word_id' => $sourceWordId,
                ]);
            }
        }
    }
}
