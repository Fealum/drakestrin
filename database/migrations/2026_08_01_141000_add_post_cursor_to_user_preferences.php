<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->unsignedInteger('read_tracking_started_post_id')->nullable()->after('read_tracking_started_at');
        });

        DB::table('user_preferences')->update([
            'read_tracking_started_post_id' => DB::table('posts')->max('id') ?: 0,
        ]);
    }

    public function down(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropColumn('read_tracking_started_post_id');
        });
    }
};
