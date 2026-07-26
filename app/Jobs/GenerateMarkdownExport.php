<?php

namespace App\Jobs;

use App\Services\MarkdownArchiveExporter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Throwable;

class GenerateMarkdownExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public function __construct(private string $path)
    {
    }

    public function handle(MarkdownArchiveExporter $exporter): void
    {
        File::ensureDirectoryExists(dirname($this->path));

        $temporaryPath = $this->path . '.tmp';

        try {
            $exporter->exportTo($temporaryPath);
            File::move($temporaryPath, $this->path);
        } catch (Throwable $exception) {
            if (File::exists($temporaryPath)) {
                File::delete($temporaryPath);
            }

            throw $exception;
        }
    }
}
