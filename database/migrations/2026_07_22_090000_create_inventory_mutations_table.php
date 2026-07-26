<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_mutations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('item_id');
            $table->string('kind', 32);
            $table->string('clock', 16);
            $table->bigInteger('effective_at');
            $table->string('source_type', 64)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->json('before_state')->nullable();
            $table->json('after_state')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['inventory_id', 'effective_at']);
            $table->index(['source_type', 'source_id']);
            $table->index(['clock', 'effective_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_mutations');
    }
};
