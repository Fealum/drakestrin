<?php

namespace App\Services\Board;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class PostComposerPreview
{
    // These are presentation hints, never a replacement for publication validation.
    public function make(array $element, array $state, array $context, array $picker, ?int $characterId, Collection $locations): ?array
    {
        $type = $element['type'] ?? 'message';
        if ($type === 'poll') {
            $deadline = $this->timestamp($element['closes_at'] ?? null);
            if (filled($element['closes_at'] ?? null) && $deadline === null) {
                return null;
            }
            $element['closes_at'] = $deadline;
            $options = array_values(array_filter(array_map(fn ($option) => trim($option ?? ''), $element['options'] ?? []), fn ($option) => $option !== ''));
            $limit = (int) ($element['max_choices'] ?? 1);
            if (trim($element['question'] ?? '') === '' || count($options) < 2 || $limit < 1 || $limit > count($options)
                || (($element['closes_at'] ?? null) !== null && $element['closes_at'] <= now()->timestamp)) {
                return null;
            }

            return ['type' => $type, 'element' => $element, 'options' => $options];
        }
        if ($type === 'scene_transition') {
            $ending = ($element['scene_action'] ?? 'start') === 'end';
            $location = $locations->firstWhere('id', $ending ? ($state['location_id'] ?? null) : ($element['location_id'] ?? null));
            $time = $this->timestamp($element['story_at'] ?? null);
            if (! $location || $time === null || ($ending && ! ($state['active'] ?? false))) {
                return null;
            }

            return ['type' => $type, 'location' => $location, 'time' => $time,
                'label' => $ending ? 'Szene beendet:' : (($state['active'] ?? false) ? 'Szene gewechselt:' : 'Szene:')];
        }
        if ($type !== 'transfer' || ! ($state['active'] ?? false) || ($state['story_at'] ?? null) === null) {
            return null;
        }
        $source = collect($picker['sourceOptions'] ?? [])->firstWhere('value', $picker['sourceValue'] ?? null);
        $recipient = collect($picker['recipientOptions'] ?? [])->firstWhere('value', $picker['recipientValue'] ?? null);
        if (! $source || ! $recipient || ($source['disabled'] ?? false) || ($recipient['disabled'] ?? false)
            || ($element['source'] ?? null) !== ($picker['sourceValue'] ?? null)) {
            return null;
        }
        [$sourceType, $sourceId] = array_pad(explode(':', $source['value'], 2), 2, null);
        $inventory = match ($sourceType) {
            'character' => ($context['characterInventories'] ?? collect())->get($characterId, collect()),
            'company_site' => ($context['sites'] ?? collect())->firstWhere('id', (int) $sourceId)?->inventory ?? collect(),
            'location' => $context['locationInventory'] ?? collect(),
            default => collect(),
        };
        $items = [];
        foreach ($element['inventory'] ?? [] as $id) {
            $stock = $inventory->firstWhere('id', (int) $id);
            if (! $stock?->item) {
                return null;
            }
            $quantity = $stock->undounitary($element['inventorystack'][$id] ?? $stock->makeunitary());
            if ($stock->item->stackable && ($quantity <= 0 || $quantity > $stock->stack)) {
                return null;
            }
            $items[] = ['name' => $stock->item->name, 'quantity' => $stock->item->stackable ? $stock->item->makeunitary($quantity) : 1];
        }

        return $items === [] ? null : ['type' => $type, 'source' => $source, 'recipient' => $recipient, 'items' => $items];
    }

    private function timestamp(mixed $value): ?int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }
        if (blank($value) || ! is_string($value)) {
            return null;
        }
        try {
            return CarbonImmutable::createFromFormat('Y-m-d\TH:i', $value, config('app.timezone'))->timestamp;
        } catch (InvalidArgumentException) {
            return null;
        }
    }
}
