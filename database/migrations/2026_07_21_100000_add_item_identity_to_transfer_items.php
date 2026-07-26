<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfer_items', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_id')->nullable()->after('item_id')->index();
            $table->text('inventory_state')->nullable()->after('inventory_id');
        });
    }

    public function down(): void
    {
        Schema::table('transfer_items', function (Blueprint $table) {
            $table->dropIndex(['inventory_id']);
            $table->dropColumn(['inventory_id', 'inventory_state']);
        });
    }
};
