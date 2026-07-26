<?php

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('thread_scene_id')->nullable()->after('post_id');
            $table->integer('story_at')->nullable()->after('thread_scene_id');
            $table->unsignedBigInteger('created_by_user_id')->nullable()->after('story_at');
            $table->timestamps();

            $table->index(['thread_scene_id', 'story_at']);
            $table->index(['sender_type', 'sender_id', 'story_at'], 'transfers_sender_story_index');
            $table->index(['recipient_type', 'recipient_id', 'story_at'], 'transfers_recipient_story_index');
        });

        DB::table('transfers')
            ->whereNotNull('post_id')
            ->orderBy('id')
            ->chunkById(100, function ($transfers) {
                foreach ($transfers as $transfer) {
                    $post = DB::table('posts')->where('id', $transfer->post_id)->first(['id', 'thread_id', 'user_id', 'time']);

                    if (! $post) {
                        continue;
                    }

                    $scene = DB::table('thread_scenes')
                        ->where('thread_id', $post->thread_id)
                        ->where(fn ($query) => $query->whereNull('starts_at_post_id')->orWhere('starts_at_post_id', '<=', $post->id))
                        ->where(fn ($query) => $query->whereNull('ends_at_post_id')->orWhere('ends_at_post_id', '>=', $post->id))
                        ->orderByDesc('id')
                        ->first(['id', 'story_started_at']);
                    $createdAt = CarbonImmutable::createFromTimestamp((int) $post->time)->toDateTimeString();

                    DB::table('transfers')->where('id', $transfer->id)->update([
                        'thread_scene_id' => $scene?->id,
                        'story_at' => $scene?->story_started_at,
                        'created_by_user_id' => $post->user_id,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropIndex(['thread_scene_id', 'story_at']);
            $table->dropIndex('transfers_sender_story_index');
            $table->dropIndex('transfers_recipient_story_index');
            $table->dropColumn(['thread_scene_id', 'story_at', 'created_by_user_id', 'created_at', 'updated_at']);
        });
    }
};
