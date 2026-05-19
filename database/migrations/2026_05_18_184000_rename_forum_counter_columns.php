<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dra_board', function (Blueprint $table) {
            $table->renameColumn('thread__total', 'thread_count');
            $table->renameColumn('post__total', 'post_count');
            $table->renameColumn('post__last', 'last_post_id');
            $table->renameColumn('post__last_time', 'last_post_at');
        });

        Schema::table('dra_thread', function (Blueprint $table) {
            $table->renameColumn('post__first_time', 'first_post_at');
            $table->renameColumn('post__total', 'post_count');
            $table->renameColumn('post__first', 'first_post_id');
            $table->renameColumn('post__last', 'last_post_id');
            $table->renameColumn('post__last_time', 'last_post_at');
        });

        Schema::table('dra_user', function (Blueprint $table) {
            $table->renameColumn('post__total', 'post_count');
        });

        Schema::table('dra_character', function (Blueprint $table) {
            $table->renameColumn('post__total', 'post_count');
        });
    }

    public function down(): void
    {
        Schema::table('dra_character', function (Blueprint $table) {
            $table->renameColumn('post_count', 'post__total');
        });

        Schema::table('dra_user', function (Blueprint $table) {
            $table->renameColumn('post_count', 'post__total');
        });

        Schema::table('dra_thread', function (Blueprint $table) {
            $table->renameColumn('last_post_at', 'post__last_time');
            $table->renameColumn('last_post_id', 'post__last');
            $table->renameColumn('first_post_id', 'post__first');
            $table->renameColumn('post_count', 'post__total');
            $table->renameColumn('first_post_at', 'post__first_time');
        });

        Schema::table('dra_board', function (Blueprint $table) {
            $table->renameColumn('last_post_at', 'post__last_time');
            $table->renameColumn('last_post_id', 'post__last');
            $table->renameColumn('post_count', 'post__total');
            $table->renameColumn('thread_count', 'thread__total');
        });
    }
};
