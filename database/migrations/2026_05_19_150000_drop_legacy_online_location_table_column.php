<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('onlines', function (Blueprint $table) {
            $table->dropColumn('table__location');
        });
    }

    public function down(): void
    {
        Schema::table('onlines', function (Blueprint $table) {
            $table->integer('table__location')->nullable()->after('action');
        });
    }
};
