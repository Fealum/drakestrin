<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->integer('post_id')->nullable()->change();
            $table->integer('sender_id')->nullable()->change();
            $table->tinyInteger('sender_type')->nullable()->change();
            $table->integer('recipient_id')->nullable()->change();
            $table->tinyInteger('recipient_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->integer('post_id')->nullable(false)->change();
            $table->integer('sender_id')->nullable(false)->change();
            $table->tinyInteger('sender_type')->nullable(false)->change();
            $table->integer('recipient_id')->nullable(false)->change();
            $table->tinyInteger('recipient_type')->nullable(false)->change();
        });
    }
};
