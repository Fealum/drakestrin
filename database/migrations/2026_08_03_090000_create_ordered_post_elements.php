<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_elements', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('post_id');
            $table->unsignedInteger('position');
            $table->string('type', 32);
            $table->timestamps();
            $table->unique(['post_id', 'position']);
            $table->index(['post_id', 'type']);
            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete();
        });

        Schema::create('post_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('post_element_id')->primary();
            $table->mediumText('message');
            $table->boolean('smilies')->default(true);
            $table->foreign('post_element_id')->references('id')->on('post_elements')->cascadeOnDelete();
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('post_element_id')->nullable()->unique()->after('post_id');
            $table->foreign('post_element_id')->references('id')->on('post_elements')->nullOnDelete();
        });

        Schema::create('post_scene_transitions', function (Blueprint $table) {
            $table->unsignedBigInteger('post_element_id')->primary();
            $table->unsignedBigInteger('ended_scene_id')->nullable()->unique();
            $table->unsignedBigInteger('started_scene_id')->nullable()->unique();
            $table->foreign('post_element_id')->references('id')->on('post_elements')->cascadeOnDelete();
            $table->foreign('ended_scene_id')->references('id')->on('thread_scenes')->nullOnDelete();
            $table->foreign('started_scene_id')->references('id')->on('thread_scenes')->nullOnDelete();
        });

        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_element_id')->unique();
            $table->string('question', 255);
            $table->string('visibility', 16)->default('anonymous');
            $table->unsignedSmallInteger('max_choices')->default(1);
            $table->unsignedInteger('closes_at')->nullable();
            $table->timestamps();
            $table->foreign('post_element_id')->references('id')->on('post_elements')->cascadeOnDelete();
        });

        Schema::create('poll_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('poll_id');
            $table->unsignedSmallInteger('position');
            $table->string('label', 255);
            $table->unsignedInteger('unattributed_votes')->default(0);
            $table->unique(['poll_id', 'position']);
            $table->foreign('poll_id')->references('id')->on('polls')->cascadeOnDelete();
        });

        Schema::create('poll_participations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('poll_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();
            $table->unique(['poll_id', 'user_id']);
            $table->foreign('poll_id')->references('id')->on('polls')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('poll_choices', function (Blueprint $table) {
            $table->unsignedBigInteger('poll_participation_id');
            $table->unsignedBigInteger('poll_option_id');
            $table->primary(['poll_participation_id', 'poll_option_id']);
            $table->foreign('poll_participation_id')->references('id')->on('poll_participations')->cascadeOnDelete();
            $table->foreign('poll_option_id')->references('id')->on('poll_options')->cascadeOnDelete();
        });

        Schema::create('post_drafts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('thread_id')->nullable();
            $table->unsignedMediumInteger('board_id')->nullable();
            $table->string('title', 225)->nullable();
            $table->unsignedInteger('character_id')->nullable();
            $table->json('payload');
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->unique(['user_id', 'thread_id']);
            $table->index(['user_id', 'updated_at']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('thread_id')->references('id')->on('threads')->cascadeOnDelete();
            $table->foreign('board_id')->references('id')->on('boards')->nullOnDelete();
            $table->foreign('character_id')->references('id')->on('characters')->nullOnDelete();
        });

        $now = now();

        DB::table('posts')->orderBy('id')->chunkById(500, function ($posts) use ($now) {
            $postIds = $posts->pluck('id');
            $transferPostIds = DB::table('transfers')->whereIn('post_id', $postIds)->pluck('post_id')->flip();
            $messagePosts = $posts->filter(fn ($post) => trim((string) $post->message) !== '' || ! $transferPostIds->has($post->id));

            if ($messagePosts->isEmpty()) {
                return;
            }

            DB::table('post_elements')->insert($messagePosts->map(fn ($post) => [
                'post_id' => $post->id,
                'position' => 100,
                'type' => 'message',
                'created_at' => $now,
                'updated_at' => $now,
            ])->all());

            $elementIds = DB::table('post_elements')->whereIn('post_id', $messagePosts->pluck('id'))->where('type', 'message')->pluck('id', 'post_id');
            DB::table('post_messages')->insert($messagePosts->map(fn ($post) => [
                'post_element_id' => $elementIds[$post->id],
                'message' => (string) $post->message,
                'smilies' => (bool) $post->smilies,
            ])->all());
        });

        DB::table('transfers')->whereNotNull('post_id')->orderBy('id')->get()->groupBy('post_id')->each(function ($transfers, $postId) use ($now) {
            $position = DB::table('post_elements')->where('post_id', $postId)->exists() ? 200 : 100;
            foreach ($transfers as $transfer) {
                $elementId = DB::table('post_elements')->insertGetId([
                    'post_id' => $postId,
                    'position' => $position,
                    'type' => 'transfer',
                    'created_at' => $transfer->created_at ?: $now,
                    'updated_at' => $transfer->updated_at ?: $now,
                ]);
                DB::table('transfers')->where('id', $transfer->id)->update(['post_element_id' => $elementId]);
                $position += 100;
            }
        });

        $this->migrateScenes($now);
        $this->migratePolls($now);
        $this->migrateSignatures();
        $this->addPollPermits();
    }

    private function migrateScenes($now): void
    {
        DB::table('thread_scenes')->orderBy('id')->get()->groupBy('thread_id')->each(function ($scenes, $threadId) use ($now) {
            $firstPostId = DB::table('threads')->where('id', $threadId)->value('first_post_id');
            $boundaries = [];

            foreach ($scenes as $scene) {
                $startPostId = $scene->starts_at_post_id ?: $firstPostId;
                if ($startPostId) {
                    $boundaries[$startPostId]['started'][] = $scene->id;
                }
                if ($scene->ends_at_post_id) {
                    $boundaries[$scene->ends_at_post_id]['ended'][] = $scene->id;
                }
            }

            foreach ($boundaries as $postId => $boundary) {
                $starts = $boundary['started'] ?? [];
                $ends = $boundary['ended'] ?? [];
                $initial = collect($scenes)->contains(fn ($scene) => ! $scene->starts_at_post_id && in_array($scene->id, $starts));
                $position = $initial ? 50 : ((int) DB::table('post_elements')->where('post_id', $postId)->max('position') + 100);
                $count = max(count($starts), count($ends), 1);

                for ($index = 0; $index < $count; $index++) {
                    $elementId = DB::table('post_elements')->insertGetId([
                        'post_id' => $postId,
                        'position' => $position + $index,
                        'type' => 'scene_transition',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    DB::table('post_scene_transitions')->insert([
                        'post_element_id' => $elementId,
                        'ended_scene_id' => $ends[$index] ?? null,
                        'started_scene_id' => $starts[$index] ?? null,
                    ]);
                }
            }
        });
    }

    private function migratePolls($now): void
    {
        if (! Schema::hasTable('dra_poll')) {
            return;
        }

        DB::table('threads')->whereNotNull('pquestion')->where('pquestion', '<>', '')->orderBy('id')->get()->each(function ($thread) use ($now) {
            if (! $thread->first_post_id) {
                return;
            }

            $elementId = DB::table('post_elements')->insertGetId([
                'post_id' => $thread->first_post_id,
                'position' => 10,
                'type' => 'poll',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $pollId = DB::table('polls')->insertGetId([
                'post_element_id' => $elementId,
                'question' => $thread->pquestion,
                'visibility' => 'anonymous',
                'max_choices' => 1,
                'closes_at' => $thread->ptimeout ? ((int) $thread->first_post_at + ((int) $thread->ptimeout * 86400)) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('dra_poll')->where('thread', $thread->id)->orderBy('id')->get()->values()->each(function ($option, $position) use ($pollId) {
                DB::table('poll_options')->insert([
                    'poll_id' => $pollId,
                    'position' => $position + 1,
                    'label' => $option->field,
                    'unattributed_votes' => $option->votes,
                ]);
            });

            if (Schema::hasTable('bb3_vote')) {
                DB::table('bb3_vote')->join('users', 'bb3_vote.userid', '=', 'users.id')->where('bb3_vote.threadid', $thread->id)
                    ->select('bb3_vote.userid')->get()->each(fn ($vote) => DB::table('poll_participations')->insertOrIgnore([
                        'poll_id' => $pollId,
                        'user_id' => $vote->userid,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]));
            }
        });
    }

    private function migrateSignatures(): void
    {
        if (! Schema::hasColumn('characters', 'signature')) {
            return;
        }

        DB::table('characters')->whereNotNull('signature')->where('signature', '<>', '')->orderBy('id')->chunkById(200, function ($characters) {
            foreach ($characters as $character) {
                if (str_contains((string) $character->usertext, (string) $character->signature)) {
                    continue;
                }
                $prefix = trim((string) $character->usertext) === '' ? '' : rtrim((string) $character->usertext)."\n\n";
                DB::table('characters')->where('id', $character->id)->update([
                    'usertext' => $prefix.'[B]Ehemalige Signatur[/B]'."\n".trim((string) $character->signature),
                ]);
            }
        });
    }

    private function addPollPermits(): void
    {
        $sourceId = DB::table('permits')->where('name', 'createpost')->value('id');
        $standard = (int) DB::table('permits')->where('id', $sourceId)->value('standard');

        foreach (['createpoll', 'votepoll'] as $name) {
            DB::table('permits')->updateOrInsert(['name' => $name], ['standard' => $standard]);
            $targetId = DB::table('permits')->where('name', $name)->value('id');
            if ($sourceId && $targetId) {
                DB::table('permissions')->where('permit_id', $sourceId)->get()->each(fn ($permission) => DB::table('permissions')->updateOrInsert([
                    'recipient_type' => $permission->recipient_type,
                    'recipient_id' => $permission->recipient_id,
                    'subject_type' => $permission->subject_type,
                    'subject_id' => $permission->subject_id,
                    'permit_id' => $targetId,
                ], ['value' => $permission->value]));
            }
        }

        Cache::flush();
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropForeign(['post_element_id']);
            $table->dropColumn('post_element_id');
        });
        Schema::dropIfExists('post_drafts');
        Schema::dropIfExists('poll_choices');
        Schema::dropIfExists('poll_participations');
        Schema::dropIfExists('poll_options');
        Schema::dropIfExists('polls');
        Schema::dropIfExists('post_scene_transitions');
        Schema::dropIfExists('post_messages');
        Schema::dropIfExists('post_elements');
    }
};
