<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('dra_item', 'items');

        Schema::table('dra_labour_component', function (Blueprint $table) {
            $table->renameColumn('item', 'item_id');
        });
    }

    public function down(): void
    {
        Schema::table('dra_labour_component', function (Blueprint $table) {
            $table->renameColumn('item_id', 'item');
        });

        Schema::rename('items', 'dra_item');
    }
};
