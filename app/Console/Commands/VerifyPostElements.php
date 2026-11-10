<?php

namespace App\Console\Commands;

use App\Services\Board\PostElementMigrationAuditor;
use Illuminate\Console\Command;

class VerifyPostElements extends Command
{
    protected $signature = 'forum:verify-post-elements {--json : Print the full audit as JSON}';

    protected $description = 'Read-only post-element integrity checks, plus legacy comparisons while their source columns remain.';

    public function handle(PostElementMigrationAuditor $auditor): int
    {
        $report = $auditor->audit();
        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
        } else {
            $this->info('Database: '.$report['database']);
            if (! $report['legacy_message_checks']) {
                $this->line('Legacy post content has been removed; checking element integrity without legacy message comparisons.');
            }
            $this->table(['Record', 'Count'], collect($report['counts'])->map(fn ($count, $name) => [$name, $count]));
            foreach ($report['issues'] as $kind => $issue) {
                $this->error($kind.': '.$issue['count'].'; sample IDs: '.implode(', ', $issue['sample_ids']));
            }
            $this->line($report['passed'] ? 'Verification passed.' : 'Verification failed. Legacy storage must be retained until every discrepancy is resolved.');
        }

        return $report['passed'] ? self::SUCCESS : self::FAILURE;
    }
}
