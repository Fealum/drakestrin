<?php

namespace App\Services\Economy;

use App\Models\Economy\CompanyWorker;
use App\Models\Economy\LabourActive;
use Illuminate\Database\Eloquent\Builder;

class LabourProcessor
{
    public function __construct(private ProductionService $production) {}

    public function processDue(?int $now = null): array
    {
        $now ??= now()->timestamp;
        $stats = [
            'processed' => 0,
            'finished' => 0,
            'paused' => 0,
            'skipped_resources' => 0,
        ];

        $strikeCutoff = $now - (CompanyWorker::STRIKE_AFTER_PERIODS * CompanyWorker::SALARY_PERIOD_SECONDS);

        LabourActive::query()
            ->whereNull('ended_at')
            ->where(function (Builder $query) use ($now, $strikeCutoff) {
                $query
                    ->where(function (Builder $query) use ($now) {
                        $query->whereNull('paused_at')->where('until', '<=', $now);
                    })
                    ->orWhere(function (Builder $query) use ($strikeCutoff) {
                        $query->whereNull('paused_at')->whereHas('companyWorker', function (Builder $query) use ($strikeCutoff) {
                            $query->whereNull('paid')->orWhere('paid', '<=', $strikeCutoff);
                        });
                    })
                    ->orWhere(function (Builder $query) use ($strikeCutoff) {
                        $query->whereNotNull('paused_at')->whereHas('companyWorker', function (Builder $query) use ($strikeCutoff) {
                            $query->whereNotNull('paid')->where('paid', '>', $strikeCutoff);
                        });
                    });
            })
            ->orderBy('until')
            ->chunkById(100, function ($activeLabours) use ($now, &$stats) {
                foreach ($activeLabours as $activeLabour) {
                    $stats[$this->production->process($activeLabour, $now)]++;
                }
            });

        return $stats;
    }
}
