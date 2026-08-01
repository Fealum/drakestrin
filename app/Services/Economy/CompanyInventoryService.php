<?php

namespace App\Services\Economy;

use App\Data\Economy\CompanyInventoryChange;
use App\Data\Economy\InventoryMutationContext;
use App\Data\Economy\InventoryStateChange;
use App\Models\Economy\CompanySite;
use App\Models\Economy\Inventory;
use App\Services\InventoryService;
use App\Support\InventoryMutationClock;
use App\Support\InventoryMutationKind;
use App\Support\InventoryStockState;
use App\Support\PermissionEntityType;
use Illuminate\Support\Facades\DB;

class CompanyInventoryService
{
    public function __construct(private InventoryService $inventory) {}

    /** @param list<CompanyInventoryChange> $changes */
    public function classifyMany(CompanySite $site, array $changes): void
    {
        DB::transaction(function () use ($site, $changes) {
            foreach ($changes as $change) {
                $this->classify(
                    $site,
                    Inventory::query()->findOrFail($change->inventoryId),
                    $change->targetWear,
                    $change->requestedQuantity,
                );
            }
        });
    }

    public function classify(
        CompanySite $site,
        Inventory $inventory,
        int $targetWear,
        int|string|null $requestedQuantity,
    ): void {
        DB::transaction(function () use ($site, $inventory, $targetWear, $requestedQuantity) {
            $inventory = Inventory::query()
                ->with('item')
                ->whereKey($inventory->id)
                ->ownedBy(PermissionEntityType::COMPANY_SITE, $site->id)
                ->where('wear', '!=', InventoryStockState::COMMITTED_TOOL->value)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $inventory->wear === $targetWear || ! $inventory->item) {
                return;
            }

            $context = new InventoryMutationContext(
                InventoryMutationKind::STATE_CHANGE,
                InventoryMutationClock::SIMULATION,
                now()->timestamp,
                'company_site',
                $site->id,
            );

            if ($inventory->item->stackable) {
                $quantity = $inventory->undounitary($requestedQuantity ?? $inventory->makeunitary());
                $quantity = min((int) $inventory->stack, max(1, $quantity));

                if ($quantity < (int) $inventory->stack) {
                    $this->inventory->take(
                        $inventory->item_id,
                        $quantity,
                        PermissionEntityType::COMPANY_SITE,
                        $site->id,
                        (int) $inventory->wear,
                        $targetWear,
                        $context,
                    );

                    return;
                }
            }

            $this->inventory->updateState($inventory, new InventoryStateChange(wear: $targetWear), $context);
        });
    }
}
