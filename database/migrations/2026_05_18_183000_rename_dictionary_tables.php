<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('dra_language', 'languages');
        Schema::rename('dra_wordtype', 'word_types');

        Schema::rename('dra_dictionary', 'words');
        Schema::table('words', function (Blueprint $table) {
            $table->renameColumn('language', 'language_id');
            $table->renameColumn('wordtype', 'word_type_id');
        });

        Schema::rename('dra_dictionarykey', 'keys');
        Schema::table('keys', function (Blueprint $table) {
            $table->renameColumn('dictionary__from', 'from_word_id');
            $table->renameColumn('dictionary__to', 'to_word_id');
        });
    }

    public function down(): void
    {
        Schema::table('keys', function (Blueprint $table) {
            $table->renameColumn('to_word_id', 'dictionary__to');
            $table->renameColumn('from_word_id', 'dictionary__from');
        });
        Schema::rename('keys', 'dra_dictionarykey');

        Schema::table('words', function (Blueprint $table) {
            $table->renameColumn('word_type_id', 'wordtype');
            $table->renameColumn('language_id', 'language');
        });
        Schema::rename('words', 'dra_dictionary');

        Schema::rename('word_types', 'dra_wordtype');
        Schema::rename('languages', 'dra_language');
    }
};
