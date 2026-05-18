<?php

namespace App\Services\Economy;

use App\Models\CompanyWorker;
use App\Models\LabourActive;
use App\Services\InventoryService;
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
            ->with(['worker.companyModel', 'labourModel.components.itemModel'])
            ->where('until', '<=', $now)
            ->orderBy('until')
            ->chunkById(100, function ($activeLabours) use ($now, &$stats) {
                foreach ($activeLabours as $activeLabour) {
                    $result = DB::transaction(fn () => $this->processOne($activeLabour->fresh(['worker.companyModel', 'labourModel.components.itemModel']), $now));
                    $stats[$result]++;
                }
            });

        return $stats;
    }

    private function processOne(?LabourActive $activeLabour, int $now): string
    {
        if (! $activeLabour || ! $activeLabour->worker || ! $activeLabour->worker->companyModel || ! $activeLabour->labourModel) {
            return 'skipped_resources';
        }

        $worker = $activeLabour->worker;
        $company = $worker->companyModel;
        $labour = $activeLabour->labourModel;

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
        $companyId = $activeLabour->worker->companyModel->id;
        $max = $requested;

        foreach ($activeLabour->labourModel->components->where('type', 0) as $component) {
            $neededPerProcess = max(1, (int) $component->quantity);
            $available = $this->inventory->available((int) $component->item, 2, $companyId, -2);
            $max = min($max, (int) floor($available / $neededPerProcess));
        }

        return $max;
    }

    private function consumeInputs(LabourActive $activeLabour, int $processes): void
    {
        $companyId = $activeLabour->worker->companyModel->id;

        foreach ($activeLabour->labourModel->components->where('type', 0) as $component) {
            $this->inventory->take((int) $component->item, (int) $component->quantity * $processes, 2, $companyId, -2);
        }
    }

    private function createOutputs(LabourActive $activeLabour, int $processes): void
    {
        $companyId = $activeLabour->worker->companyModel->id;

        foreach ($activeLabour->labourModel->components->where('type', 2) as $component) {
            $this->inventory->add((int) $component->item, (int) $component->quantity * $processes, 2, $companyId, (int) $activeLabour->prodas);
        }
    }

    private function returnTools(LabourActive $activeLabour, int $companyId): void
    {
        foreach ($activeLabour->labourModel->components->where('type', 1) as $component) {
            $this->inventory->take((int) $component->item, (int) $component->quantity, 2, $companyId, -3, -2);
        }
    }
}
