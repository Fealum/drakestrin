<?php

namespace App\Services\Economy;

use App\Models\Economy\CompanyWorker;
use App\Models\Economy\LabourActive;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class LabourProcessor
{
    private const MAX_EVENTS_PER_PASS = 500;

    private const PROCESS_LOCK_SECONDS = 300;

    public function __construct(private ProductionService $production) {}

    public function processDue(?int $now = null): array
    {
        $now ??= now()->timestamp;
        $stats = $this->emptyStats();
        $lock = Cache::lock('economy:labour-processing', self::PROCESS_LOCK_SECONDS);

        if (! $lock->get()) {
            $stats['busy'] = true;

            return $stats;
        }

        try {
            return $this->processDueEvents($now, $stats);
        } finally {
            $lock->release();
        }
    }

    private function processDueEvents(int $now, array $stats): array
    {
        $blockedIds = [];

        for ($event = 0; $event < self::MAX_EVENTS_PER_PASS; $event++) {
            $activeLabour = $this->dueQuery($now)
                ->when($blockedIds !== [], fn (Builder $query) => $query->whereNotIn('id', $blockedIds))
                ->orderBy('until')
                ->orderBy('id')
                ->first();

            if (! $activeLabour) {
                return $stats;
            }

            $result = $this->production->process($activeLabour, $now);
            $stats[$result]++;

            if ($result === 'skipped_resources') {
                $blockedIds[] = $activeLabour->id;
            }
        }

        $stats['limit_reached'] = $this->dueQuery($now)->exists();

        return $stats;
    }

    private function dueQuery(int $now): Builder
    {
        $strikeCutoff = $now - (CompanyWorker::STRIKE_AFTER_PERIODS * CompanyWorker::SALARY_PERIOD_SECONDS);

        return LabourActive::query()
            ->whereNull('ended_at')
            ->whereHas('companyWorker.company')
            ->whereHas('labour')
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
            });
    }

    private function emptyStats(): array
    {
        return [
            'processed' => 0,
            'finished' => 0,
            'paused' => 0,
            'skipped_resources' => 0,
            'limit_reached' => false,
            'busy' => false,
        ];
    }
}
