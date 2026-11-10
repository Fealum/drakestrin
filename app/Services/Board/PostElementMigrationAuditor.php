<?php

namespace App\Services\Board;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PostElementMigrationAuditor
{
    public function audit(): array
    {
        $counts = [];
        $issues = [];
        $record = function (string $kind, int $id) use (&$issues) {
            $issues[$kind]['count'] = ($issues[$kind]['count'] ?? 0) + 1;
            if (count($issues[$kind]['sample_ids'] ?? []) < 10) {
                $issues[$kind]['sample_ids'][] = $id;
            }
        };

        foreach (['posts', 'post_elements', 'post_messages', 'transfers', 'thread_scenes', 'post_scene_transitions', 'polls', 'poll_options', 'poll_participations'] as $table) {
            $counts[$table] = DB::table($table)->count();
        }

        DB::table('posts')->orderBy('id')->chunkById(500, function ($posts) use ($record) {
            $messages = DB::table('post_elements')->join('post_messages', 'post_messages.post_element_id', '=', 'post_elements.id')
                ->whereIn('post_id', $posts->pluck('id'))->orderBy('position')
                ->get(['post_id', 'message', 'smilies'])->groupBy('post_id');
            $elementPosts = DB::table('post_elements')->whereIn('post_id', $posts->pluck('id'))->pluck('post_id')->flip();
            foreach ($posts as $post) {
                if (! $elementPosts->has($post->id)) {
                    $record('posts_without_elements', $post->id);
                }
                $rows = $messages->get($post->id, collect());
                $text = $rows->pluck('message')->filter(fn ($message) => $message !== '')->implode("\n\n");
                if ($text !== (string) $post->message) {
                    $record('message_body_mismatches', $post->id);
                }
                if ($rows->count() === 1 && (bool) $rows->first()->smilies !== (bool) $post->smilies) {
                    $record('message_smiley_mismatches', $post->id);
                }
            }
        });

        foreach (['message' => 'post_messages', 'transfer' => 'transfers', 'scene_transition' => 'post_scene_transitions', 'poll' => 'polls'] as $type => $table) {
            DB::table('post_elements as e')->leftJoin($table.' as s', 's.post_element_id', '=', 'e.id')
                ->where('e.type', $type)->whereNull('s.post_element_id')->pluck('e.id')
                ->each(fn ($id) => $record('elements_missing_subtype', (int) $id));
        }
        DB::table('transfers as t')->leftJoin('post_elements as e', 'e.id', '=', 't.post_element_id')
            ->whereNotNull('t.post_id')->where(function ($query) {
                $query->whereNull('e.id')->orWhereColumn('e.post_id', '!=', 't.post_id')->orWhere('e.type', '!=', 'transfer');
            })->pluck('t.id')->each(fn ($id) => $record('transfer_link_mismatches', (int) $id));

        foreach (['started_scene_id' => 'starts_at_post_id', 'ended_scene_id' => 'ends_at_post_id'] as $reference => $anchor) {
            $query = DB::table('thread_scenes as s')->join('threads as t', 't.id', '=', 's.thread_id')
                ->leftJoin('post_scene_transitions as x', 'x.'.$reference, '=', 's.id')
                ->leftJoin('post_elements as e', 'e.id', '=', 'x.post_element_id');
            if ($anchor === 'ends_at_post_id') {
                $query->whereNotNull('s.'.$anchor);
            }
            $expected = $anchor === 'starts_at_post_id' ? 'COALESCE(s.starts_at_post_id, t.first_post_id)' : 's.ends_at_post_id';
            $query->where(function ($query) use ($expected) {
                $query->whereNull('e.id')->orWhereRaw('e.post_id <> '.$expected);
            })->pluck('s.id')->each(fn ($id) => $record('scene_anchor_mismatches', (int) $id));
        }

        $counts['legacy_polls'] = 0;
        $counts['legacy_options'] = 0;
        $counts['legacy_votes'] = 0;
        if (Schema::hasTable('dra_poll')) {
            $legacy = DB::table('threads')->whereNotNull('pquestion')->where('pquestion', '<>', '')->get();
            $counts['legacy_polls'] = $legacy->count();
            foreach ($legacy as $thread) {
                $poll = DB::table('polls as p')->join('post_elements as e', 'e.id', '=', 'p.post_element_id')
                    ->where('e.post_id', $thread->first_post_id)->where('p.question', $thread->pquestion)->first(['p.*']);
                $options = DB::table('dra_poll')->where('thread', $thread->id)->orderBy('id')->get();
                $counts['legacy_options'] += $options->count();
                $counts['legacy_votes'] += (int) $options->sum('votes');
                if (! $poll) {
                    $record('legacy_polls_missing', $thread->id);

                    continue;
                }
                $deadline = $thread->ptimeout ? (int) $thread->first_post_at + (int) $thread->ptimeout * 86400 : null;
                if ($poll->visibility !== 'anonymous' || $poll->closes_at !== $deadline) {
                    $record('legacy_poll_settings_mismatches', $thread->id);
                }
                $newOptions = DB::table('poll_options')->where('poll_id', $poll->id)->orderBy('position')->get();
                foreach ($options as $index => $option) {
                    $new = $newOptions->get($index);
                    if (! $new || $new->label !== $option->field || $new->unattributed_votes < $option->votes) {
                        $record('legacy_option_or_vote_loss', $option->id);
                    }
                }
                if (Schema::hasTable('bb3_vote')) {
                    $missing = DB::table('bb3_vote as v')->join('users as u', 'u.id', '=', 'v.userid')
                        ->where('v.threadid', $thread->id)
                        ->whereNotExists(function ($query) use ($poll) {
                            $query->selectRaw('1')->from('poll_participations as p')->where('p.poll_id', $poll->id)->whereColumn('p.user_id', 'v.userid');
                        })->distinct()->pluck('v.userid');
                    $missing->each(fn ($id) => $record('legacy_participation_loss', (int) $id));
                }
            }
        }
        $counts['legacy_signatures'] = 0;
        if (Schema::hasColumn('characters', 'signature')) {
            DB::table('characters')->whereNotNull('signature')->where('signature', '<>', '')->orderBy('id')
                ->each(function ($character) use (&$counts, $record) {
                    $signature = trim($character->signature);
                    if ($signature === '') {
                        return;
                    }
                    $counts['legacy_signatures']++;
                    if (! str_contains((string) $character->usertext, $signature)) {
                        $record('legacy_signature_loss', $character->id);
                    }
                });
        }

        return ['database' => DB::connection()->getDatabaseName(), 'counts' => $counts, 'issues' => $issues, 'passed' => $issues === []];
    }
}
