<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('post_drafts', function (Blueprint $table) {
            $table->boolean('creates_character')->default(false)->after('character_id');
            $table->string('new_character_name', 85)->nullable()->after('creates_character');
        });
    }

    public function down(): void
    {
        Schema::table('post_drafts', function (Blueprint $table) {
            $table->dropColumn(['creates_character', 'new_character_name']);
        });
    }
};
