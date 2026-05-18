<?php

namespace App\Console\Commands;

use App\Models\Board\Board;
use App\Models\Board\Thread;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportBoardThreads extends Command
{
    protected $signature = 'forum:export-board {board : Board ID} {--path= : Write the Markdown export to this file instead of stdout}';

    protected $description = 'Export a board and its subboards as Markdown.';

    public function handle(): int
    {
        $board = Board::find((int) $this->argument('board'));

        if (! $board) {
            $this->error('Board not found.');

            return self::FAILURE;
        }

        $markdown = $this->renderBoard($board);
        $path = $this->option('path');

        if ($path) {
            File::put($path, $markdown);
            $this->info('Export written to ' . $path);
        } else {
            $this->line($markdown);
        }

        return self::SUCCESS;
    }

    private function renderBoard(Board $board): string
    {
        $lines = [
            '# Forum ' . $board->id . ': ' . $board->name,
            '',
        ];

        $threads = Thread::with('posts.characterModel')
            ->where('board', $board->id)
            ->orderByDesc('important')
            ->orderByDesc('post__last_time')
            ->orderBy('id')
            ->get();

        foreach ($threads as $thread) {
            $lines[] = '## Thema ' . $thread->id . ': ' . $thread->name;
            $lines[] = '';

            foreach ($thread->posts as $post) {
                $author = $post->characterModel?->name ?? 'Unbekannter Charakter';
                $time = $post->time?->format('Y-m-d H:i:s') ?? '';

                $lines[] = '### Beitrag von ' . $author . ', ' . $time;
                $lines[] = '';
                $lines[] = (string) $post->message;
                $lines[] = '';
            }
        }

        $childBoards = Board::where('board', $board->id)
            ->orderBy('sort')
            ->orderByRaw('LOWER(name)')
            ->get();

        foreach ($childBoards as $subBoard) {
            $lines[] = rtrim($this->renderBoard($subBoard));
            $lines[] = '';
        }

        return rtrim(implode(PHP_EOL, $lines)) . PHP_EOL;
    }
}
