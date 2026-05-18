<?php

namespace App\Console\Commands;

use App\Services\Economy\LabourProcessor;
use Illuminate\Console\Command;

class ProcessLabour extends Command
{
    protected $signature = 'labour:process';

    protected $description = 'Process due company labour tasks.';

    public function handle(LabourProcessor $processor): int
    {
        $stats = $processor->processDue();

        $this->info(sprintf(
            'Processed: %d, finished: %d, skipped unpaid: %d, skipped resources: %d',
            $stats['processed'],
            $stats['finished'],
            $stats['skipped_unpaid'],
            $stats['skipped_resources'],
        ));

        return self::SUCCESS;
    }
}
