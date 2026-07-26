<?php

namespace App\Services\Economy;

use App\Data\Economy\InventoryMutationContext;
use App\Data\Economy\InventoryStateChange;
use App\Models\Economy\Company;
use App\Models\Economy\Inventory;
use App\Services\InventoryService;
use App\Support\InventoryMutationClock;
use App\Support\InventoryMutationKind;
use App\Support\PermissionEntityType;
use Illuminate\Support\Facades\DB;

class CompanyInventoryService
{
    public function __construct(private InventoryService $inventory) {}

    public function classify(
        Company $company,
        Inventory $inventory,
        int $targetWear,
        int|string|null $requestedQuantity,
    ): void {
        DB::transaction(function () use ($company, $inventory, $targetWear, $requestedQuantity) {
            $inventory = Inventory::query()
                ->with('item')
                ->whereKey($inventory->id)
                ->ownedBy(PermissionEntityType::COMPANY, $company->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $inventory->wear === $targetWear || ! $inventory->item) {
                return;
            }

            $context = new InventoryMutationContext(
                InventoryMutationKind::STATE_CHANGE,
                InventoryMutationClock::SIMULATION,
                now()->timestamp,
                'company',
                $company->id,
            );

            if ($inventory->item->stackable) {
                $quantity = $inventory->undounitary($requestedQuantity ?? $inventory->makeunitary());
                $quantity = min((int) $inventory->stack, max(1, $quantity));

                if ($quantity < (int) $inventory->stack) {
                    $this->inventory->take(
                        $inventory->item_id,
                        $quantity,
                        PermissionEntityType::COMPANY,
                        $company->id,
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
