<?php

namespace App\Console\Commands;

use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread;
use App\Services\Board\ForumCounters;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairForumCounters extends Command
{
    protected $signature = 'forum:repair-counters
        {--dry-run : Report mismatches without updating rows}
        {--threads : Repair thread counters only}
        {--boards : Repair board counters only}
        {--users : Repair user post counters only}
        {--characters : Repair character post counters only}
        {--thread=* : Limit thread repairs to one or more thread IDs}
        {--board=* : Limit board repairs to one or more board IDs}
        {--user=* : Limit user repairs to one or more user IDs}
        {--character=* : Limit character repairs to one or more character IDs}';

    protected $description = 'Repair denormalized forum counters for threads, boards, users, and characters.';

    public function handle(ForumCounters $counters): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $selected = collect(['threads', 'boards', 'users', 'characters'])
            ->filter(fn (string $option) => (bool) $this->option($option));

        if ($selected->isEmpty()) {
            $selected = collect(['threads', 'boards', 'users', 'characters']);
        }

        $repaired = 0;

        if ($selected->contains('threads')) {
            $repaired += $this->repairThreads($counters, $dryRun);
        }

        if ($selected->contains('boards')) {
            $repaired += $this->repairBoards($counters, $dryRun);
        }

        if ($selected->contains('users')) {
            $repaired += $this->repairUsers($counters, $dryRun);
        }

        if ($selected->contains('characters')) {
            $repaired += $this->repairCharacters($counters, $dryRun);
        }

        $this->info(($dryRun ? 'Mismatches found: ' : 'Rows repaired: ') . $repaired);

        return self::SUCCESS;
    }

    private function repairThreads(ForumCounters $counters, bool $dryRun): int
    {
        $count = 0;

        Thread::query()
            ->when($this->ids('thread'), fn ($query, array $ids) => $query->whereIn('id', $ids))
            ->orderBy('id')
            ->chunkById(200, function ($threads) use ($counters, $dryRun, &$count) {
                foreach ($threads as $thread) {
                    $expected = $this->expectedThreadCounters($thread);

                    if (! $this->differs($thread, $expected)) {
                        continue;
                    }

                    $count++;
                    $this->line('Thread #' . $thread->id . ' counters differ.');

                    if (! $dryRun) {
                        $counters->refreshThread($thread);
                    }
                }
            });

        return $count;
    }

    private function repairBoards(ForumCounters $counters, bool $dryRun): int
    {
        $count = 0;

        Board::query()
            ->when($this->ids('board'), fn ($query, array $ids) => $query->whereIn('id', $ids))
            ->orderBy('id')
            ->chunkById(200, function ($boards) use ($counters, $dryRun, &$count) {
                foreach ($boards as $board) {
                    $expected = $this->expectedBoardCounters($board);

                    if (! $this->differs($board, $expected)) {
                        continue;
                    }

                    $count++;
                    $this->line('Board #' . $board->id . ' counters differ.');

                    if (! $dryRun) {
                        $counters->refreshBoard($board);
                    }
                }
            });

        return $count;
    }

    private function repairUsers(ForumCounters $counters, bool $dryRun): int
    {
        $count = 0;

        DB::table('dra_user')
            ->select(['id', 'post__total'])
            ->when($this->ids('user'), fn ($query, array $ids) => $query->whereIn('id', $ids))
            ->orderBy('id')
            ->chunkById(200, function ($users) use ($counters, $dryRun, &$count) {
                foreach ($users as $user) {
                    $expected = Post::where('user', $user->id)->count();

                    if ((int) $user->post__total === $expected) {
                        continue;
                    }

                    $count++;
                    $this->line('User #' . $user->id . ' post counter differs.');

                    if (! $dryRun) {
                        $counters->refreshUser((int) $user->id);
                    }
                }
            });

        return $count;
    }

    private function repairCharacters(ForumCounters $counters, bool $dryRun): int
    {
        $count = 0;

        DB::table('dra_character')
            ->select(['id', 'post__total'])
            ->when($this->ids('character'), fn ($query, array $ids) => $query->whereIn('id', $ids))
            ->orderBy('id')
            ->chunkById(200, function ($characters) use ($counters, $dryRun, &$count) {
                foreach ($characters as $character) {
                    $expected = Post::where('character', $character->id)->count();

                    if ((int) $character->post__total === $expected) {
                        continue;
                    }

                    $count++;
                    $this->line('Character #' . $character->id . ' post counter differs.');

                    if (! $dryRun) {
                        $counters->refreshCharacter((int) $character->id);
                    }
                }
            });

        return $count;
    }

    private function expectedThreadCounters(Thread $thread): array
    {
        $firstPost = Post::query()
            ->where('thread', $thread->id)
            ->orderBy('time')
            ->orderBy('id')
            ->first();

        $lastPost = Post::query()
            ->where('thread', $thread->id)
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->first();

        return [
            'post__total' => Post::where('thread', $thread->id)->count(),
            'post__first' => $firstPost?->id ?? 0,
            'post__first_time' => $firstPost?->time?->timestamp ?? 0,
            'post__last' => $lastPost?->id ?? 0,
            'post__last_time' => $lastPost?->time?->timestamp ?? 0,
        ];
    }

    private function expectedBoardCounters(Board $board): array
    {
        $lastPost = Post::query()
            ->where('board', $board->id)
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->first();

        return [
            'thread__total' => Thread::where('board', $board->id)->count(),
            'post__total' => Post::where('board', $board->id)->count(),
            'post__last' => $lastPost?->id ?? 0,
            'post__last_time' => $lastPost?->time?->timestamp ?? 0,
        ];
    }

    private function differs($model, array $expected): bool
    {
        foreach ($expected as $key => $value) {
            if ((int) $model->getRawOriginal($key) !== (int) $value) {
                return true;
            }
        }

        return false;
    }

    private function ids(string $option): array
    {
        return collect($this->option($option))
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }
}
