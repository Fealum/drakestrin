<?php

namespace App\Services\Economy;

use App\Data\Economy\InventoryMutationContext;
use App\Data\Economy\StartProductionData;
use App\Exceptions\Economy\InsufficientProductionResources;
use App\Models\Economy\Company;
use App\Models\Economy\CompanySite;
use App\Models\Economy\CompanyWorker;
use App\Models\Economy\Labour;
use App\Models\Economy\LabourActive;
use App\Models\Economy\ProductionRun;
use App\Services\InventoryService;
use App\Support\InventoryMutationClock;
use App\Support\InventoryMutationKind;
use App\Support\InventoryStockState;
use App\Support\PermissionEntityType;
use App\Support\ProductionPauseReason;
use Illuminate\Support\Facades\DB;

class ProductionService
{
    private const RESOURCE_RETRY_SECONDS = 60;

    public function __construct(private InventoryService $inventory) {}

    public function start(
        CompanyWorker $worker,
        Labour $labour,
        StartProductionData $data,
        int $capacity,
        ?int $now = null,
    ): ?LabourActive {
        $now ??= now()->timestamp;

        try {
            return DB::transaction(function () use ($worker, $labour, $data, $capacity, $now) {
                $worker = CompanyWorker::query()->whereKey($worker->id)->lockForUpdate()->firstOrFail();
                $worker->loadMissing('site');
                $site = $worker->site;

                if (! $site?->location_id) {
                    return null;
                }
                $company = Company::query()->whereKey($worker->company_id)->lockForUpdate()->firstOrFail();
                $labour->loadMissing('components.item');
                $instances = min(
                    $data->instances,
                    max(0, $capacity),
                    $this->availableCapacity($worker, $labour),
                    $this->maxInstancesByResources($site, $labour),
                );

                if ($instances < 1) {
                    return null;
                }

                $inputs = $this->componentSnapshot($labour, 0, $instances);
                $tools = $this->componentSnapshot($labour, 1, $instances);
                $outputs = $this->componentSnapshot($labour, 2, $instances);
                $activeLabour = LabourActive::create([
                    'company_worker_id' => $worker->id,
                    'labour_id' => $labour->id,
                    'since' => $now,
                    'until' => $now + max(1, (int) $labour->duration),
                    'prodas' => $data->outputState,
                    'quantity' => $data->quantity,
                    'instances' => $instances,
                    'nextinsta' => 0,
                    'input_items' => $inputs,
                    'output_items' => $outputs,
                    'tool_items' => $tools,
                ]);
                $run = $this->createRun($activeLabour, $company, $site, $worker, $labour, $inputs, $outputs, $now);

                $this->moveItems(
                    $site,
                    $tools,
                    InventoryStockState::PRODUCTION->value,
                    InventoryStockState::COMMITTED_TOOL->value,
                    $this->context($run, InventoryMutationKind::STATE_CHANGE, $now),
                );
                $this->moveItems(
                    $site,
                    $inputs,
                    InventoryStockState::PRODUCTION->value,
                    null,
                    $this->context($run, InventoryMutationKind::CONSUMPTION, $now),
                );

                return $activeLabour;
            });
        } catch (InsufficientProductionResources) {
            return null;
        }
    }

    public function process(LabourActive $activeLabour, int $now): string
    {
        return DB::transaction(function () use ($activeLabour, $now) {
            $activeLabour = LabourActive::query()
                ->whereKey($activeLabour->id)
                ->whereNull('ended_at')
                ->lockForUpdate()
                ->first();

            if (! $activeLabour) {
                return 'skipped_resources';
            }

            $activeLabour->load(['companyWorker.site.company', 'labour.components.item', 'currentRun']);
            $worker = $activeLabour->companyWorker;
            $site = $worker?->site;
            $company = $site?->company;
            $labour = $activeLabour->labour;

            if (! $worker || ! $site || ! $company || ! $labour || ! $site->location_id) {
                return 'skipped_resources';
            }

            Company::query()->whereKey($company->id)->lockForUpdate()->firstOrFail();
            $run = $activeLabour->currentRun;

            if (! $run && $activeLabour->runs()->doesntExist()) {
                $run = $this->bootstrapLegacyRun($activeLabour, $company, $site, $worker, $labour);
            }

            if ($worker->isOnStrikeAt($now)) {
                $strikeStartedAt = $worker->strikeStartedAt() ?? $now;

                if ($run && ($run->due_at?->timestamp ?? PHP_INT_MAX) <= $strikeStartedAt) {
                    return $this->completeRun(
                        $activeLabour,
                        $run,
                        $company,
                        $site,
                        $worker,
                        $labour,
                        $now,
                    );
                }

                $this->pauseForStrike($activeLabour, $strikeStartedAt);

                return 'paused';
            }

            if ($activeLabour->pause_reason === ProductionPauseReason::STRIKE) {
                $this->resumeAfterStrike($activeLabour, $run, $now);

                if ($run) {
                    return 'processed';
                }
            }

            if (! $run) {
                if ($activeLabour->stop_requested_at) {
                    $this->finish($activeLabour, $site, $now);

                    return 'finished';
                }

                if ($this->startNextRun($activeLabour, $company, $site, $worker, $labour, $now)) {
                    return 'processed';
                }

                $activeLabour->update(['until' => $now + self::RESOURCE_RETRY_SECONDS]);

                return 'skipped_resources';
            }

            if (($run->due_at?->timestamp ?? PHP_INT_MAX) > $now) {
                return 'processed';
            }

            return $this->completeRun($activeLabour, $run, $company, $site, $worker, $labour, $now);
        });
    }

    private function completeRun(
        LabourActive $activeLabour,
        ProductionRun $run,
        Company $company,
        CompanySite $site,
        CompanyWorker $worker,
        Labour $labour,
        int $now,
    ): string {
        $completedAt = min($now, $run->due_at?->timestamp ?? $now);
        $this->createOutputs($run, $site, $completedAt);
        $run->update(['completed_at' => $completedAt]);
        $remaining = (int) $activeLabour->quantity === -1
            ? -1
            : max(0, (int) $activeLabour->quantity - 1);
        $activeLabour->update(['quantity' => $remaining]);

        if ($activeLabour->stop_requested_at || $remaining === 0) {
            $this->finish($activeLabour, $site, $completedAt);

            return 'finished';
        }

        if (! $this->startNextRun($activeLabour, $company, $site, $worker, $labour, $completedAt)) {
            $activeLabour->update(['until' => $now + self::RESOURCE_RETRY_SECONDS]);
        }

        return 'processed';
    }

    private function pauseForStrike(LabourActive $activeLabour, int $strikeStartedAt): void
    {
        if ($activeLabour->pause_reason === ProductionPauseReason::STRIKE) {
            return;
        }

        $activeLabour->update([
            'paused_at' => $strikeStartedAt,
            'pause_reason' => ProductionPauseReason::STRIKE,
        ]);
    }

    private function resumeAfterStrike(LabourActive $activeLabour, ?ProductionRun $run, int $now): void
    {
        $pausedAt = $activeLabour->paused_at?->timestamp ?? $now;
        $pausedFor = max(0, $now - $pausedAt);

        if ($run) {
            $dueAt = ($run->due_at?->timestamp ?? $now) + $pausedFor;
            $run->update(['due_at' => $dueAt]);
            $activeLabour->until = $dueAt;
        }

        $activeLabour->paused_at = null;
        $activeLabour->pause_reason = null;
        $activeLabour->save();
    }

    public function requestStop(LabourActive $activeLabour, ?int $now = null): string
    {
        $now ??= now()->timestamp;

        return DB::transaction(function () use ($activeLabour, $now) {
            $activeLabour = LabourActive::query()
                ->whereKey($activeLabour->id)
                ->whereNull('ended_at')
                ->lockForUpdate()
                ->firstOrFail();
            $activeLabour->load(['companyWorker.site.company', 'currentRun']);
            $site = $activeLabour->companyWorker?->site;
            $company = $site?->company;

            if (! $company || ! $site) {
                abort(404);
            }

            Company::query()->whereKey($company->id)->lockForUpdate()->firstOrFail();

            if ($activeLabour->currentRun) {
                $activeLabour->update(['stop_requested_at' => $now]);

                return 'stopping';
            }

            $this->finish($activeLabour, $site, $now);

            return 'stopped';
        });
    }

    public function maxInstancesByResources(CompanySite $site, Labour $labour): int
    {
        $maximum = PHP_INT_MAX;
        $requirements = collect([
            ...$this->componentSnapshot($labour, 0, 1),
            ...$this->componentSnapshot($labour, 1, 1),
        ])->groupBy('item_id');

        foreach ($requirements as $itemId => $items) {
            $required = max(1, $items->sum('quantity'));
            $available = $this->inventory->available(
                (int) $itemId,
                PermissionEntityType::COMPANY_SITE,
                $site->id,
                InventoryStockState::PRODUCTION->value,
            );
            $maximum = min($maximum, (int) floor($available / $required));
        }

        return $maximum === PHP_INT_MAX ? PHP_INT_MAX : max(0, $maximum);
    }

    private function startNextRun(
        LabourActive $activeLabour,
        Company $company,
        CompanySite $site,
        CompanyWorker $worker,
        Labour $labour,
        int $now,
    ): bool {
        $this->ensureSnapshots($activeLabour, $labour);
        $inputs = $activeLabour->input_items ?? [];

        if (! $this->itemsAvailable($site, $inputs, InventoryStockState::PRODUCTION->value)) {
            return false;
        }

        $run = $this->createRun(
            $activeLabour,
            $company,
            $site,
            $worker,
            $labour,
            $inputs,
            $activeLabour->output_items ?? [],
            $now,
        );
        $this->moveItems(
            $site,
            $inputs,
            InventoryStockState::PRODUCTION->value,
            null,
            $this->context($run, InventoryMutationKind::CONSUMPTION, $now),
        );

        return true;
    }

    private function bootstrapLegacyRun(
        LabourActive $activeLabour,
        Company $company,
        CompanySite $site,
        CompanyWorker $worker,
        Labour $labour,
    ): ProductionRun {
        $this->ensureSnapshots($activeLabour, $labour);

        return ProductionRun::create([
            'labour_active_id' => $activeLabour->id,
            'company_id' => $company->id,
            'company_site_id' => $site->id,
            'company_worker_id' => $worker->id,
            'labour_id' => $labour->id,
            'labour_name' => $labour->name,
            'worker_name' => $worker->name,
            'instances' => $activeLabour->instances,
            'output_state' => $activeLabour->prodas,
            'inputs' => $activeLabour->input_items ?? [],
            'outputs' => $activeLabour->output_items ?? [],
            'started_at' => $activeLabour->since?->timestamp ?? now()->timestamp,
            'due_at' => $activeLabour->until?->timestamp ?? now()->timestamp,
        ]);
    }

    private function createRun(
        LabourActive $activeLabour,
        Company $company,
        CompanySite $site,
        CompanyWorker $worker,
        Labour $labour,
        array $inputs,
        array $outputs,
        int $startedAt,
    ): ProductionRun {
        $dueAt = $startedAt + max(1, (int) $labour->duration);
        $activeLabour->update(['until' => $dueAt]);

        return ProductionRun::create([
            'labour_active_id' => $activeLabour->id,
            'company_id' => $company->id,
            'company_site_id' => $site->id,
            'company_worker_id' => $worker->id,
            'labour_id' => $labour->id,
            'labour_name' => $labour->name,
            'worker_name' => $worker->name,
            'instances' => $activeLabour->instances,
            'output_state' => $activeLabour->prodas,
            'inputs' => $inputs,
            'outputs' => $outputs,
            'started_at' => $startedAt,
            'due_at' => $dueAt,
        ]);
    }

    private function createOutputs(ProductionRun $run, CompanySite $site, int $now): void
    {
        foreach ($run->outputs as $output) {
            $created = $this->inventory->add(
                (int) $output['item_id'],
                (int) $output['quantity'],
                PermissionEntityType::COMPANY_SITE,
                $site->id,
                $run->output_state,
                $this->context($run, InventoryMutationKind::PRODUCTION, $now),
            );

            if ($created !== (int) $output['quantity']) {
                throw new InsufficientProductionResources('A production output item no longer exists.');
            }
        }
    }

    private function finish(LabourActive $activeLabour, CompanySite $site, int $now): void
    {
        foreach ($activeLabour->tool_items ?? [] as $tool) {
            $this->inventory->take(
                (int) $tool['item_id'],
                (int) $tool['quantity'],
                PermissionEntityType::COMPANY_SITE,
                $site->id,
                InventoryStockState::COMMITTED_TOOL->value,
                InventoryStockState::PRODUCTION->value,
                new InventoryMutationContext(
                    InventoryMutationKind::STATE_CHANGE,
                    InventoryMutationClock::SIMULATION,
                    $now,
                    'labour_active',
                    $activeLabour->id,
                ),
            );
        }

        $activeLabour->update([
            'quantity' => 0,
            'until' => $now,
            'ended_at' => $now,
        ]);
    }

    private function moveItems(
        CompanySite $site,
        array $items,
        int $fromState,
        ?int $toState,
        InventoryMutationContext $context,
    ): void {
        foreach ($items as $item) {
            $quantity = (int) $item['quantity'];
            $moved = $this->inventory->take(
                (int) $item['item_id'],
                $quantity,
                PermissionEntityType::COMPANY_SITE,
                $site->id,
                $fromState,
                $toState,
                $context,
            );

            if ($moved !== $quantity) {
                throw new InsufficientProductionResources('Production inputs or tools are unavailable.');
            }
        }
    }

    private function itemsAvailable(CompanySite $site, array $items, int $state): bool
    {
        foreach ($items as $item) {
            if ($this->inventory->available(
                (int) $item['item_id'],
                PermissionEntityType::COMPANY_SITE,
                $site->id,
                $state,
            ) < (int) $item['quantity']) {
                return false;
            }
        }

        return true;
    }

    private function ensureSnapshots(LabourActive $activeLabour, Labour $labour): void
    {
        if ($activeLabour->input_items !== null && $activeLabour->output_items !== null && $activeLabour->tool_items !== null) {
            return;
        }

        $activeLabour->update([
            'input_items' => $this->componentSnapshot($labour, 0, $activeLabour->instances),
            'output_items' => $this->componentSnapshot($labour, 2, $activeLabour->instances),
            'tool_items' => $this->componentSnapshot($labour, 1, $activeLabour->instances),
        ]);
        $activeLabour->refresh();
    }

    /** @return list<array{item_id:int,name:string,quantity:int}> */
    private function componentSnapshot(Labour $labour, int $type, int $instances): array
    {
        return $labour->components
            ->where('type', $type)
            ->groupBy('item_id')
            ->map(fn ($components, $itemId) => [
                'item_id' => (int) $itemId,
                'name' => (string) $components->first()?->item?->name,
                'quantity' => $components->sum(fn ($component) => (int) $component->quantity) * $instances,
            ])
            ->values()
            ->all();
    }

    private function availableCapacity(CompanyWorker $worker, Labour $labour): int
    {
        $worker->load('activeLabours.labour');
        $workload = $worker->activeLabours->sum(function (LabourActive $activeLabour) {
            $capacity = (int) ($activeLabour->labour?->workload ?? 0);

            return $capacity > 0 ? (1 / $capacity) * (int) $activeLabour->instances : 0;
        });

        return (int) floor(max(0, 1 - $workload) * max(0, (int) $labour->workload));
    }

    private function context(ProductionRun $run, InventoryMutationKind $kind, int $effectiveAt): InventoryMutationContext
    {
        return new InventoryMutationContext(
            $kind,
            InventoryMutationClock::SIMULATION,
            $effectiveAt,
            'production_run',
            $run->id,
        );
    }
}
