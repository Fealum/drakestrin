<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('avatar_character_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('users')->whereNull('avatar_character_id')->update(['avatar_character_id' => 0]);
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('avatar_character_id')->nullable(false)->default(0)->change();
        });
    }
};
