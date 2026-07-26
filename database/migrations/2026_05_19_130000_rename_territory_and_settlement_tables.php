<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dra_territory', function (Blueprint $table) {
            $table->renameColumn('territory', 'parent_id');
            $table->renameColumn('settlement', 'capital_id');
        });
        Schema::rename('dra_territory', 'territories');

        Schema::rename('dra_settlement', 'settlements');
    }

    public function down(): void
    {
        Schema::rename('settlements', 'dra_settlement');

        Schema::rename('territories', 'dra_territory');
        Schema::table('dra_territory', function (Blueprint $table) {
            $table->renameColumn('capital_id', 'settlement');
            $table->renameColumn('parent_id', 'territory');
        });
    }
};
