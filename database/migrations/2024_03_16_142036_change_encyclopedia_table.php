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
        Schema::rename('dra_encyclopedia', 'pages');

        // Rename columns according to Eloquent conventions
        Schema::table('pages', function (Blueprint $table) {
            $table->renameColumn('time', 'created_at');
            $table->renameColumn('user', 'user_id');
            $table->renameColumn('encyclopedia', 'page_id');
            $table->string('slug', length: 255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Undo rename table according to Eloquent conventions
        Schema::rename('pages', 'dra_encyclopedia');

        // Undo rename columns according to Eloquent conventions
        Schema::table('dra_encyclopedia', function (Blueprint $table) {
            $table->renameColumn('created_at', 'time');
            $table->renameColumn('user_id', 'user');
            $table->renameColumn('page_id', 'encyclopedia');
            $table->dropColumn('slug');
        });
    }
};
