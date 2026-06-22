<?php

namespace App\Http\Controllers;

use App\Services\MarkdownArchiveExporter;
use App\Services\PermissionService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class MarkdownExportController extends Controller
{
    public function __construct(PermissionService $permissionService, private MarkdownArchiveExporter $exporter)
    {
        parent::__construct($permissionService);
    }

    public function __invoke(): BinaryFileResponse|Response
    {
        if (! $this->permissionService->allows('exportmarkdown', user: request()->user())) {
            abort(403);
        }

        $directory = storage_path('app/exports');
        File::ensureDirectoryExists($directory);

        $path = $directory . '/markdown-export-' . now()->format('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.zip';

        try {
            $this->exporter->exportTo($path);
        } catch (Throwable $exception) {
            if (File::exists($path)) {
                File::delete($path);
            }

            report($exception);

            return response('Markdown export could not be created.', 500);
        }

        return response()
            ->download($path, 'drakestrin-markdown-export.zip', [
                'Content-Type' => 'application/zip',
            ])
            ->deleteFileAfterSend();
    }
}
