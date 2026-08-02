<?php

use App\Support\PermissionEntityType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->boolean('auto_subscribe')->default(true);
            $table->string('default_email_frequency', 24)->default('once_until_read');
            $table->unsignedInteger('read_tracking_started_at');
            $table->unsignedInteger('last_daily_digest_at')->nullable();
            $table->unsignedInteger('last_weekly_digest_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('thread_reads', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('thread_id');
            $table->unsignedInteger('last_read_post_id');
            $table->unsignedInteger('read_at');
            $table->primary(['user_id', 'thread_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('thread_id')->references('id')->on('threads')->cascadeOnDelete();
        });

        Schema::create('thread_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('thread_id');
            $table->string('email_frequency', 24)->default('once_until_read');
            $table->unsignedInteger('last_emailed_post_id')->nullable();
            $table->unsignedInteger('created_at');
            $table->unsignedInteger('updated_at');
            $table->unique(['user_id', 'thread_id']);
            $table->index(['email_frequency', 'user_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('thread_id')->references('id')->on('threads')->cascadeOnDelete();
        });

        $now = now()->timestamp;

        DB::table('users')->orderBy('id')->chunkById(500, function ($users) use ($now) {
            DB::table('user_preferences')->insert(
                $users->map(fn ($user) => [
                    'user_id' => $user->id,
                    'auto_subscribe' => (bool) $user->always_activate_notify,
                    'default_email_frequency' => $user->receiveemails ? 'once_until_read' : 'none',
                    'read_tracking_started_at' => $now,
                    'last_daily_digest_at' => $now,
                    'last_weekly_digest_at' => $now,
                ])->all()
            );
        });

        if (Schema::hasTable('bb3_notify')) {
            DB::table('bb3_notify')
                ->join('users', 'bb3_notify.userid', '=', 'users.id')
                ->join('threads', 'bb3_notify.threadid', '=', 'threads.id')
                ->select('bb3_notify.userid', 'bb3_notify.threadid', 'threads.last_post_id')
                ->orderBy('bb3_notify.userid')
                ->chunk(500, function ($subscriptions) use ($now) {
                    DB::table('thread_subscriptions')->insertOrIgnore(
                        $subscriptions->map(fn ($subscription) => [
                            'user_id' => $subscription->userid,
                            'thread_id' => $subscription->threadid,
                            'email_frequency' => 'immediate',
                            'last_emailed_post_id' => $subscription->last_post_id ?: null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ])->all()
                    );
                });

            Schema::drop('bb3_notify');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('always_activate_notify');
        });

        DB::table('permits')->updateOrInsert(
            ['name' => 'viewthreadsubscriptions'],
            ['standard' => 0],
        );

        $permitId = DB::table('permits')->where('name', 'viewthreadsubscriptions')->value('id');
        $administratorGroupId = DB::table('groups')->where('name', 'Administrator')->value('id');

        if ($permitId && $administratorGroupId) {
            DB::table('permissions')->updateOrInsert([
                'recipient_type' => PermissionEntityType::GROUP->value,
                'recipient_id' => $administratorGroupId,
                'subject_type' => 0,
                'subject_id' => 0,
                'permit_id' => $permitId,
            ], ['value' => 2]);
        }

        Cache::flush();
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'always_activate_notify')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('always_activate_notify')->default(false)->after('mute');
            });

            DB::table('users')
                ->join('user_preferences', 'users.id', '=', 'user_preferences.user_id')
                ->update(['users.always_activate_notify' => DB::raw('user_preferences.auto_subscribe')]);
        }

        if (! Schema::hasTable('bb3_notify')) {
            Schema::create('bb3_notify', function (Blueprint $table) {
                $table->unsignedInteger('threadid')->default(0);
                $table->unsignedInteger('userid')->default(0);
            });

            DB::table('bb3_notify')->insertUsing(
                ['threadid', 'userid'],
                DB::table('thread_subscriptions')->select('thread_id', 'user_id'),
            );
        }

        $permitId = DB::table('permits')->where('name', 'viewthreadsubscriptions')->value('id');

        if ($permitId) {
            DB::table('permissions')->where('permit_id', $permitId)->delete();
        }

        DB::table('permits')->where('name', 'viewthreadsubscriptions')->delete();
        Schema::dropIfExists('thread_subscriptions');
        Schema::dropIfExists('thread_reads');
        Schema::dropIfExists('user_preferences');
        Cache::flush();
    }
};
