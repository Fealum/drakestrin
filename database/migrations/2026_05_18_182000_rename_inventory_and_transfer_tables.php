<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('dra_inventory', 'inventories');
        Schema::table('inventories', function (Blueprint $table) {
            $table->renameColumn('item', 'item_id');
            $table->renameColumn('table__owner', 'owner_type');
            $table->renameColumn('owner', 'owner_id');
        });

        Schema::rename('dra_transfer', 'transfers');
        Schema::table('transfers', function (Blueprint $table) {
            $table->renameColumn('post', 'post_id');
            $table->renameColumn('table__sender', 'sender_type');
            $table->renameColumn('sender', 'sender_id');
            $table->renameColumn('table__recipient', 'recipient_type');
            $table->renameColumn('recipient', 'recipient_id');
        });

        Schema::rename('dra_transferitem', 'transfer_items');
        Schema::table('transfer_items', function (Blueprint $table) {
            $table->renameColumn('transfer', 'transfer_id');
            $table->renameColumn('item', 'item_id');
        });
    }

    public function down(): void
    {
        Schema::table('transfer_items', function (Blueprint $table) {
            $table->renameColumn('item_id', 'item');
            $table->renameColumn('transfer_id', 'transfer');
        });
        Schema::rename('transfer_items', 'dra_transferitem');

        Schema::table('transfers', function (Blueprint $table) {
            $table->renameColumn('recipient_id', 'recipient');
            $table->renameColumn('recipient_type', 'table__recipient');
            $table->renameColumn('sender_id', 'sender');
            $table->renameColumn('sender_type', 'table__sender');
            $table->renameColumn('post_id', 'post');
        });
        Schema::rename('transfers', 'dra_transfer');

        Schema::table('inventories', function (Blueprint $table) {
            $table->renameColumn('owner_id', 'owner');
            $table->renameColumn('owner_type', 'table__owner');
            $table->renameColumn('item_id', 'item');
        });
        Schema::rename('inventories', 'dra_inventory');
    }
};
