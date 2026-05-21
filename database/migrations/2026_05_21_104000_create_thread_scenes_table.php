<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thread_scenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('thread_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('starts_at_post_id')->nullable();
            $table->unsignedBigInteger('ends_at_post_id')->nullable();
            $table->integer('story_started_at')->nullable();
            $table->integer('story_ended_at')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->index(['thread_id', 'starts_at_post_id']);
            $table->index(['location_id', 'thread_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thread_scenes');
    }
};
