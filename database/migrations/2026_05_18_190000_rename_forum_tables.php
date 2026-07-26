<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dra_board', function (Blueprint $table) {
            $table->renameColumn('board', 'parent_id');
        });

        Schema::table('dra_thread', function (Blueprint $table) {
            $table->renameColumn('board', 'board_id');
        });

        Schema::table('dra_post', function (Blueprint $table) {
            $table->renameColumn('board', 'board_id');
            $table->renameColumn('thread', 'thread_id');
        });

        Schema::rename('dra_board', 'boards');
        Schema::rename('dra_thread', 'threads');
        Schema::rename('dra_post', 'posts');
    }

    public function down(): void
    {
        Schema::rename('posts', 'dra_post');
        Schema::rename('threads', 'dra_thread');
        Schema::rename('boards', 'dra_board');

        Schema::table('dra_post', function (Blueprint $table) {
            $table->renameColumn('thread_id', 'thread');
            $table->renameColumn('board_id', 'board');
        });

        Schema::table('dra_thread', function (Blueprint $table) {
            $table->renameColumn('board_id', 'board');
        });

        Schema::table('dra_board', function (Blueprint $table) {
            $table->renameColumn('parent_id', 'board');
        });
    }
};
