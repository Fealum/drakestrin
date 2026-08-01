<?php

namespace App\Http\Requests\Economy;

use App\Data\Economy\CompanyInventoryChange;
use App\Models\Economy\Company;
use App\Models\Economy\CompanySite;
use App\Models\Economy\Inventory;
use App\Support\Currency;
use App\Support\InventoryStockState;
use App\Support\PermissionEntityType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCompanyInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $site = $this->route('site');

        return $site instanceof CompanySite
            && ($this->user()?->can('manageInventory', $site) ?? false);
    }

    protected function prepareForValidation(): void
    {
        $inventory = $this->input('inventory', []);

        if (! is_array($inventory)) {
            return;
        }

        foreach ($inventory as &$change) {
            if (! is_array($change) || ! is_array($change['price'] ?? null)) {
                continue;
            }

            foreach (['til', 'tuk', 'ten'] as $denomination) {
                if (($change['price'][$denomination] ?? null) === null || $change['price'][$denomination] === '') {
                    $change['price'][$denomination] = 0;
                }
            }
        }
        unset($change);

        $this->merge(['inventory' => $inventory]);
    }

    public function rules(): array
    {
        return [
            'inventory' => ['required', 'array', 'min:1'],
            'inventory.*' => ['required', 'array:state,price,quantity,members'],
            'inventory.*.state' => ['required', Rule::in(['production', 'reserved', 'sale'])],
            'inventory.*.price' => ['required', 'array:til,tuk,ten'],
            'inventory.*.price.til' => ['required', 'integer', 'between:0,1000000000'],
            'inventory.*.price.tuk' => ['required', 'integer', 'between:0,1000000000'],
            'inventory.*.price.ten' => ['required', 'integer', 'between:0,1000000000'],
            'inventory.*.quantity' => ['nullable', 'string', 'max:40'],
            'inventory.*.members' => ['nullable', 'array', 'min:1'],
            'inventory.*.members.*' => ['required', 'integer', 'distinct', 'min:1'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $inventory = $this->input('inventory', []);
            if (! is_array($inventory)) {
                return;
            }

            $submittedIds = [];

            foreach ($inventory as $inventoryId => $change) {
                if (! is_array($change) || ! ctype_digit((string) $inventoryId) || (int) $inventoryId < 1) {
                    $validator->errors()->add('inventory', 'Ein Inventareintrag ist ungültig.');

                    continue;
                }

                $memberIds = Arr::get($change, 'members');
                if ($memberIds === null) {
                    $memberIds = [(int) $inventoryId];
                } elseif (! is_array($memberIds) || $memberIds === []) {
                    $validator->errors()->add("inventory.{$inventoryId}.members", 'Die Gegenstandsgruppe ist ungültig.');

                    continue;
                }

                $memberIds = array_map('intval', $memberIds);
                if (! in_array((int) $inventoryId, $memberIds, true)) {
                    $validator->errors()->add("inventory.{$inventoryId}.members", 'Die Gegenstandsgruppe ist ungültig.');
                }

                if (count($memberIds) > 1) {
                    $quantity = Arr::get($change, 'quantity');
                    if (! ctype_digit((string) $quantity) || (int) $quantity < 1 || (int) $quantity > count($memberIds)) {
                        $validator->errors()->add("inventory.{$inventoryId}.quantity", 'Die Menge muss zwischen 1 und dem Bestand liegen.');
                    }
                }

                array_push($submittedIds, ...$memberIds);
            }
            $ids = array_values(array_unique($submittedIds));
            if (count($ids) !== count($submittedIds)) {
                $validator->errors()->add('inventory', 'Ein Inventareintrag wurde mehrfach übermittelt.');
            }

            $company = $this->route('company');
            $site = $this->route('site');
            if (
                $company instanceof Company && $site instanceof CompanySite
                && (int) $site->company_id === (int) $company->id
                && count($ids) !== Inventory::query()
                ->ownedBy(PermissionEntityType::COMPANY_SITE, $site->id)
                ->where('wear', '!=', InventoryStockState::COMMITTED_TOOL->value)
                ->whereKey($ids)
                ->count()
            ) {
                $validator->errors()->add('inventory', 'Mindestens ein Inventareintrag gehört nicht zu diesem Betrieb.');
            }
        }];
    }

    /** @return list<CompanyInventoryChange> */
    public function changes(): array
    {
        return collect($this->validated('inventory'))
            ->flatMap(function (array $change, int|string $inventoryId): array {
                $targetWear = $this->targetWear($change);
                $memberIds = array_values(array_map('intval', $change['members'] ?? []));

                if ($memberIds !== []) {
                    $quantity = min(count($memberIds), max(1, (int) ($change['quantity'] ?? count($memberIds))));

                    return array_map(
                        fn(int $memberId) => new CompanyInventoryChange($memberId, $targetWear, null),
                        array_slice($memberIds, 0, $quantity),
                    );
                }

                return [new CompanyInventoryChange(
                    inventoryId: (int) $inventoryId,
                    targetWear: $targetWear,
                    requestedQuantity: $change['quantity'] ?? null,
                )];
            })
            ->values()
            ->all();
    }

    private function targetWear(array $change): int
    {
        return match ($change['state']) {
            'production' => InventoryStockState::PRODUCTION->value,
            'reserved' => InventoryStockState::RESERVED->value,
            'sale' => Currency::toTen(
                (int) $change['price']['til'],
                (int) $change['price']['tuk'],
                (int) $change['price']['ten'],
            ),
        };
    }
}
