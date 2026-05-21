<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('parent_type');
            $table->unsignedBigInteger('parent_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('priority')->default(0);
            $table->geometry('geom')->nullable();
            $table->index(['parent_type', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
