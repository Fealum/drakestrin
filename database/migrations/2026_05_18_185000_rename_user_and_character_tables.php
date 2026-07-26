<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dra_user', function (Blueprint $table) {
            $table->renameColumn('character__avatar', 'avatar_character_id');
        });

        Schema::table('dra_character', function (Blueprint $table) {
            $table->renameColumn('user', 'user_id');
        });

        Schema::table('dra_post', function (Blueprint $table) {
            $table->renameColumn('user', 'user_id');
            $table->renameColumn('character', 'character_id');
        });

        Schema::table('dra_company', function (Blueprint $table) {
            $table->renameColumn('character', 'character_id');
        });

        Schema::table('dra_online', function (Blueprint $table) {
            $table->renameColumn('user', 'user_id');
        });

        Schema::table('dra_user_contact', function (Blueprint $table) {
            $table->renameColumn('user', 'user_id');
        });

        Schema::table('dra_territory', function (Blueprint $table) {
            $table->renameColumn('character', 'character_id');
        });

        Schema::rename('dra_user', 'users');
        Schema::rename('dra_character', 'characters');
        Schema::rename('dra_user_contact', 'user_contacts');
    }

    public function down(): void
    {
        Schema::rename('user_contacts', 'dra_user_contact');
        Schema::rename('characters', 'dra_character');
        Schema::rename('users', 'dra_user');

        Schema::table('dra_territory', function (Blueprint $table) {
            $table->renameColumn('character_id', 'character');
        });

        Schema::table('dra_user_contact', function (Blueprint $table) {
            $table->renameColumn('user_id', 'user');
        });

        Schema::table('dra_online', function (Blueprint $table) {
            $table->renameColumn('user_id', 'user');
        });

        Schema::table('dra_company', function (Blueprint $table) {
            $table->renameColumn('character_id', 'character');
        });

        Schema::table('dra_post', function (Blueprint $table) {
            $table->renameColumn('character_id', 'character');
            $table->renameColumn('user_id', 'user');
        });

        Schema::table('dra_character', function (Blueprint $table) {
            $table->renameColumn('user_id', 'user');
        });

        Schema::table('dra_user', function (Blueprint $table) {
            $table->renameColumn('avatar_character_id', 'character__avatar');
        });
    }
};
