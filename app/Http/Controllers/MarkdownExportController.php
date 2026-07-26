<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateMarkdownExport;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MarkdownExportController extends Controller
{
    public function __construct(PermissionService $permissionService)
    {
        parent::__construct($permissionService);
    }

    public function index(): View
    {
        $this->authorizeExport();

        return view('export.markdown', [
            'exports' => $this->exports(),
        ]);
    }

    public function store(): RedirectResponse
    {
        $this->authorizeExport();

        File::ensureDirectoryExists($this->directory());

        $filename = 'markdown-export-' . now()->format('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.zip';
        GenerateMarkdownExport::dispatch($this->path($filename));

        return redirect()
            ->route('export.markdown')
            ->with('export_markdown_status', 'Der Markdown-Export wurde in die Warteschlange gestellt.');
    }

    public function download(string $filename): BinaryFileResponse
    {
        $this->authorizeExport();

        $path = $this->path($filename);

        abort_unless(File::exists($path), 404);

        return response()
            ->download($path, $filename, [
                'Content-Type' => 'application/zip',
            ]);
    }

    public function destroy(string $filename): RedirectResponse
    {
        $this->authorizeExport();

        $path = $this->path($filename);

        abort_unless(File::exists($path), 404);

        File::delete($path);

        return redirect()
            ->route('export.markdown')
            ->with('export_markdown_status', 'Der Markdown-Export wurde gelöscht.');
    }

    private function authorizeExport(): void
    {
        if (! $this->permissionService->allows('exportmarkdown', user: request()->user())) {
            abort(403);
        }
    }

    private function exports(): array
    {
        File::ensureDirectoryExists($this->directory());

        return collect(File::files($this->directory()))
            ->filter(fn ($file) => $file->isFile() && strtolower($file->getExtension()) === 'zip')
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(fn ($file) => [
                'filename' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'createdAt' => Carbon::createFromTimestamp($file->getMTime()),
            ])
            ->values()
            ->all();
    }

    private function path(string $filename): string
    {
        abort_unless($this->isValidFilename($filename), 404);

        return $this->directory() . '/' . $filename;
    }

    private function directory(): string
    {
        return storage_path('app/exports');
    }

    private function isValidFilename(string $filename): bool
    {
        return basename($filename) === $filename
            && str_ends_with(strtolower($filename), '.zip')
            && preg_match('/^[A-Za-z0-9._-]+\.zip$/', $filename) === 1;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1024 * 1024 * 1024) {
            return number_format($bytes / (1024 * 1024 * 1024), 2, ',', '.') . ' GB';
        }

        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 2, ',', '.') . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2, ',', '.') . ' KB';
        }

        return number_format($bytes, 0, ',', '.') . ' B';
    }
}
