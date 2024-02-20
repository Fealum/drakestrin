<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename table according to Eloquent conventions
        Schema::rename('dra_conversation', 'messages');

        // Rename columns according to Eloquent conventions
        Schema::table('messages', function (Blueprint $table) {
            $table->renameColumn('time', 'created_at');
            $table->renameColumn('user__sender', 'sender_user_id');
            $table->renameColumn('user__recipient', 'recipient_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename table
        Schema::rename('messages', 'dra_conversation');

        // Rename columns 
        Schema::table('dra_conversation', function (Blueprint $table) {
            $table->renameColumn('created_at', 'time');
            $table->renameColumn('sender_user_id', 'user__sender');
            $table->renameColumn('recipient_user_id', 'user__recipient');
        });
    }
};
