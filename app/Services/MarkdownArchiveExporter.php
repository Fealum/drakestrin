<?php

namespace App\Services;

use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\Encyclopedia\Page;
use App\Services\Board\PostMarkdownRenderer;
use Illuminate\Support\Facades\Gate;
use RuntimeException;
use ZipArchive;

class MarkdownArchiveExporter
{
    public function exportTo(string $path, ?array $boardIds = null, ?array $pageIds = null): void
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('The PHP zip extension is not available.');
        }

        $temporaryFiles = [];
        $zip = new ZipArchive;

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Could not create markdown export archive.');
        }

        try {
            $zip->addEmptyDir('board');
            $zip->addEmptyDir('encyclopedia');

            $boardQuery = Board::query()
                ->orderBy('sort')
                ->orderByRaw('LOWER(name)');

            if ($boardIds === null) {
                $boardQuery->where('parent_id', 0);
            } else {
                $boardQuery->whereIn('id', $boardIds);
            }

            $boardQuery->get()
                ->each(fn (Board $board) => $this->addBoard($zip, $board, 'board', $temporaryFiles));

            $pageQuery = Page::query()
                ->orderBy('sort')
                ->orderBy('name')
                ->orderBy('id');

            if ($pageIds === null) {
                $pageQuery->where('page_id', 0);
            } else {
                $pageQuery->whereIn('id', $pageIds);
            }

            $pageQuery->cursor()
                ->each(fn (Page $page) => $this->addPage($zip, $page, 'encyclopedia', $temporaryFiles));

            if ($zip->close() !== true) {
                throw new RuntimeException('Could not finalize markdown export archive.');
            }
        } finally {
            foreach ($temporaryFiles as $temporaryFile) {
                if (is_file($temporaryFile)) {
                    unlink($temporaryFile);
                }
            }
        }
    }

    public function __construct(
        private PermissionService $permissions,
        private PostMarkdownRenderer $postMarkdown,
    ) {}

    private function addBoard(ZipArchive $zip, Board $board, string $parentPath, array &$temporaryFiles): void
    {
        if (Gate::denies('view', $board)) {
            return;
        }

        $boardPath = $parentPath.'/'.$this->entryName($board->id, $board->name);
        $zip->addEmptyDir($boardPath);

        ForumThread::query()
            ->where('board_id', $board->id)
            ->orderByDesc('important')
            ->orderByDesc('last_post_at')
            ->orderBy('id')
            ->cursor()
            ->filter(fn (ForumThread $thread) => Gate::allows('view', $thread))
            ->each(function (ForumThread $thread) use ($zip, $board, $boardPath, &$temporaryFiles) {
                $this->addMarkdownFile(
                    $zip,
                    $boardPath.'/'.$this->entryName($thread->id, $thread->name).'.md',
                    fn ($handle) => $this->writeThread($handle, $thread, $board),
                    $temporaryFiles
                );
            });

        $board->children()
            ->get()
            ->each(fn (Board $child) => $this->addBoard($zip, $child, $boardPath, $temporaryFiles));
    }

    private function addPage(ZipArchive $zip, Page $page, string $parentPath, array &$temporaryFiles): void
    {
        if (! $this->permissions->allows('show', $page)) {
            return;
        }

        $entryName = $this->entryName($page->id, $page->name);

        $this->addMarkdownFile(
            $zip,
            $parentPath.'/'.$entryName.'.md',
            fn ($handle) => $this->writePage($handle, $page),
            $temporaryFiles
        );

        $children = $page->children()
            ->orderBy('id')
            ->get();

        if ($children->isEmpty()) {
            return;
        }

        $childrenPath = $parentPath.'/'.$entryName;
        $zip->addEmptyDir($childrenPath);

        $children->each(fn (Page $child) => $this->addPage($zip, $child, $childrenPath, $temporaryFiles));
    }

    private function writeThread($handle, ForumThread $thread, Board $board): void
    {
        $this->writeLines($handle, [
            '# Thema: '.$thread->name.' (ID: '.$thread->id.')',
            'Board: '.$board->name.' (ID: '.$board->id.')',
            'Erstellt: '.$this->isoDate($thread->first_post_at),
            '',
        ]);

        Post::query()
            ->with(['character', 'elements.message', 'elements.transfer.items.item', 'elements.sceneTransition.endedScene.location', 'elements.sceneTransition.startedScene.location', 'elements.poll.options'])
            ->where('thread_id', $thread->id)
            ->orderBy('time')
            ->orderBy('id')
            ->cursor()
            ->each(function (Post $post, int $index) use ($handle) {
                $characterName = $post->character?->name ?? 'Unbekannter Charakter';
                $characterId = $post->character_id ?: 'unbekannt';

                $this->writeLines($handle, [
                    '## Beitrag '.($index + 1),
                    'Charakter: '.$characterName.' (ID: '.$characterId.')',
                    'Erstellt: '.$this->isoDate($post->time),
                    '',
                    '---',
                    '',
                    $this->postMarkdown->render($post),
                    '',
                ]);
            });
    }

    private function writePage($handle, Page $page): void
    {
        $title = $page->title ?: $page->name;

        $this->writeLines($handle, [
            '# '.$title.' (ID: '.$page->id.')',
            '',
            (string) $page->text,
        ]);
    }

    private function addMarkdownFile(ZipArchive $zip, string $zipPath, callable $writer, array &$temporaryFiles): void
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'markdown-export-');

        if ($temporaryFile === false) {
            throw new RuntimeException('Could not create temporary markdown export file.');
        }

        $temporaryFiles[] = $temporaryFile;
        $handle = fopen($temporaryFile, 'wb');

        if ($handle === false) {
            throw new RuntimeException('Could not open temporary markdown export file.');
        }

        try {
            $writer($handle);
        } finally {
            fclose($handle);
        }

        if ($zip->addFile($temporaryFile, $zipPath) !== true) {
            throw new RuntimeException('Could not add markdown file to export archive.');
        }
    }

    private function writeLines($handle, array $lines): void
    {
        foreach ($lines as $line) {
            fwrite($handle, rtrim($line).PHP_EOL);
        }
    }

    private function entryName(int $id, string $name): string
    {
        return str_pad((string) $id, 4, '0', STR_PAD_LEFT).' '.$this->sanitizePathSegment($name);
    }

    private function sanitizePathSegment(string $value): string
    {
        $value = preg_replace('/[\/\\\\\x00-\x1F\x7F]+/', ' ', $value) ?? '';
        $value = preg_replace('/\s+/', ' ', $value) ?? '';
        $value = trim($value, " .\t\n\r\0\x0B");

        return $value !== '' ? $value : 'unbenannt';
    }

    private function isoDate($date): string
    {
        return $date?->toIso8601String() ?? '';
    }
}
