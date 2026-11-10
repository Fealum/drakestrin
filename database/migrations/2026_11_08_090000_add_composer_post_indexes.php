<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['character_id', 'thread_id', 'time'], 'posts_character_thread_time_index');
            $table->index(['thread_id', 'id'], 'posts_thread_cursor_index');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_character_thread_time_index');
            $table->dropIndex('posts_thread_cursor_index');
        });
    }
};
