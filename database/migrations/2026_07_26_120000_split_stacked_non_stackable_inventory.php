<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $inventories = DB::table('inventories')
                ->join('items', 'items.id', '=', 'inventories.item_id')
                ->where('items.stackable', 0)
                ->where('inventories.stack', '>', 0)
                ->orderBy('inventories.id')
                ->get([
                    'inventories.id',
                    'inventories.item_id',
                    'inventories.stack',
                    'inventories.wear',
                    'inventories.owner_id',
                    'inventories.owner_type',
                    'inventories.timelastvalue',
                    'inventories.data',
                ]);

            foreach ($inventories as $inventory) {
                $mutations = DB::table('inventory_mutations')
                    ->where('inventory_id', $inventory->id)
                    ->orderBy('id')
                    ->get();

                DB::table('inventories')->where('id', $inventory->id)->update(['stack' => 0]);
                $this->normalizeMutationStates($mutations);

                for ($instance = 1; $instance < (int) $inventory->stack; $instance++) {
                    $inventoryId = DB::table('inventories')->insertGetId([
                        'item_id' => $inventory->item_id,
                        'stack' => 0,
                        'wear' => $inventory->wear,
                        'owner_id' => $inventory->owner_id,
                        'owner_type' => $inventory->owner_type,
                        'timelastvalue' => $inventory->timelastvalue,
                        'data' => $inventory->data,
                    ]);

                    foreach ($mutations as $mutation) {
                        DB::table('inventory_mutations')->insert([
                            'inventory_id' => $inventoryId,
                            'item_id' => $mutation->item_id,
                            'kind' => $mutation->kind,
                            'clock' => $mutation->clock,
                            'effective_at' => $mutation->effective_at,
                            'source_type' => $mutation->source_type,
                            'source_id' => $mutation->source_id,
                            'before_state' => $this->normalizeState($mutation->before_state),
                            'after_state' => $this->normalizeState($mutation->after_state),
                            'created_at' => $mutation->created_at,
                        ]);
                    }
                }
            }
        });
    }

    public function down(): void
    {
        // Individual item identities cannot be combined again without losing information.
    }

    private function normalizeMutationStates(iterable $mutations): void
    {
        foreach ($mutations as $mutation) {
            DB::table('inventory_mutations')->where('id', $mutation->id)->update([
                'before_state' => $this->normalizeState($mutation->before_state),
                'after_state' => $this->normalizeState($mutation->after_state),
            ]);
        }
    }

    private function normalizeState(?string $state): ?string
    {
        if ($state === null) {
            return null;
        }

        $decoded = json_decode($state, true, flags: JSON_THROW_ON_ERROR);
        Arr::set($decoded, 'stack', 0);

        return json_encode($decoded, JSON_THROW_ON_ERROR);
    }
};
