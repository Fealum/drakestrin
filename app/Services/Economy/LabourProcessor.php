<?php

namespace App\Services\Economy;

use App\Models\Economy\CompanyWorker;
use App\Models\Economy\LabourActive;
use App\Services\InventoryService;
use App\Support\PermissionEntityType;
use Illuminate\Support\Facades\DB;

class LabourProcessor
{
    public function __construct(private InventoryService $inventory)
    {
    }

    public function processDue(?int $now = null): array
    {
        $now ??= now()->timestamp;
        $stats = [
            'processed' => 0,
            'finished' => 0,
            'skipped_unpaid' => 0,
            'skipped_resources' => 0,
        ];

        LabourActive::query()
            ->with(['companyWorker.company', 'labour.components.item'])
            ->where('until', '<=', $now)
            ->orderBy('until')
            ->chunkById(100, function ($activeLabours) use ($now, &$stats) {
                foreach ($activeLabours as $activeLabour) {
                    $result = DB::transaction(fn () => $this->processOne($activeLabour->fresh(['companyWorker.company', 'labour.components.item']), $now));
                    $stats[$result]++;
                }
            });

        return $stats;
    }

    private function processOne(?LabourActive $activeLabour, int $now): string
    {
        if (! $activeLabour || ! $activeLabour->companyWorker || ! $activeLabour->companyWorker->company || ! $activeLabour->labour) {
            return 'skipped_resources';
        }

        $worker = $activeLabour->companyWorker;
        $company = $worker->company;
        $labour = $activeLabour->labour;

        if (($worker->paid?->timestamp ?? 0) <= $now - 7776000) {
            return 'skipped_unpaid';
        }

        $duration = max(1, (int) $labour->duration);
        $processes = (int) ceil(($now - ($activeLabour->until?->timestamp ?? $now)) / $duration);
        $processes = max(1, $processes);
        $finished = false;

        if ((int) $activeLabour->quantity !== -1 && (int) $activeLabour->quantity <= $processes) {
            $processes = (int) $activeLabour->quantity;
            $finished = true;
        }

        $totalProcesses = $processes * max(1, (int) $activeLabour->instances);
        $maxByResources = $this->maxProcessesByInputs($activeLabour, $totalProcesses);

        if ($maxByResources <= 0) {
            return 'skipped_resources';
        }

        if ($totalProcesses > $maxByResources) {
            $totalProcesses = $maxByResources;
            $processes = max(1, (int) floor($totalProcesses / max(1, (int) $activeLabour->instances)));
            $finished = false;
        }

        $this->consumeInputs($activeLabour, $totalProcesses);
        $this->createOutputs($activeLabour, $totalProcesses);

        if ($finished) {
            $this->returnTools($activeLabour, $company->id);
            $activeLabour->delete();

            return 'finished';
        }

        $activeLabour->update([
            'quantity' => (int) $activeLabour->quantity === -1 ? -1 : max(0, (int) $activeLabour->quantity - $processes),
            'until' => ($activeLabour->until?->timestamp ?? $now) + ($duration * ($processes + 1)),
            'instances' => (int) $activeLabour->instances,
            'nextinsta' => (int) $activeLabour->nextinsta,
        ]);

        return 'processed';
    }

    private function maxProcessesByInputs(LabourActive $activeLabour, int $requested): int
    {
        $companyId = $activeLabour->companyWorker->company->id;
        $max = $requested;

        foreach ($activeLabour->labour->components->where('type', 0) as $component) {
            $neededPerProcess = max(1, (int) $component->quantity);
            $available = $this->inventory->available((int) $component->item_id, PermissionEntityType::COMPANY, $companyId, -2);
            $max = min($max, (int) floor($available / $neededPerProcess));
        }

        return $max;
    }

    private function consumeInputs(LabourActive $activeLabour, int $processes): void
    {
        $companyId = $activeLabour->companyWorker->company->id;

        foreach ($activeLabour->labour->components->where('type', 0) as $component) {
            $this->inventory->take((int) $component->item_id, (int) $component->quantity * $processes, PermissionEntityType::COMPANY, $companyId, -2);
        }
    }

    private function createOutputs(LabourActive $activeLabour, int $processes): void
    {
        $companyId = $activeLabour->companyWorker->company->id;

        foreach ($activeLabour->labour->components->where('type', 2) as $component) {
            $this->inventory->add((int) $component->item_id, (int) $component->quantity * $processes, PermissionEntityType::COMPANY, $companyId, (int) $activeLabour->prodas);
        }
    }

    private function returnTools(LabourActive $activeLabour, int $companyId): void
    {
        foreach ($activeLabour->labour->components->where('type', 1) as $component) {
            $this->inventory->take((int) $component->item_id, (int) $component->quantity, PermissionEntityType::COMPANY, $companyId, -3, -2);
        }
    }
}
