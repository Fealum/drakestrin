<?php

namespace App\Console\Commands;

use App\Services\Economy\InventoryConsistencyAuditor;
use Illuminate\Console\Command;

class AuditInventoryTimeline extends Command
{
    protected $signature = 'economy:audit-inventory
        {--transfer=* : Limit the audit to transfer IDs}
        {--inventory=* : Limit the mutation audit to inventory IDs}';

    protected $description = 'Audit transfers, item identities, mutation chains, and historical inventory balances.';

    public function handle(InventoryConsistencyAuditor $auditor): int
    {
        $transferIds = $this->idsFromOption('transfer');
        $inventoryIds = $this->idsFromOption('inventory');
        $issues = $auditor->audit($transferIds, $inventoryIds);

        foreach ($issues as $issue) {
            $this->error($issue);
        }

        $this->info('Issues found: '.count($issues));

        return $issues ? self::FAILURE : self::SUCCESS;
    }

    /** @return list<int> */
    private function idsFromOption(string $option): array
    {
        return collect($this->option($option))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
