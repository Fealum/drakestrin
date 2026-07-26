<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('reversal_of_transfer_id')->nullable()->after('id');
            $table->unique('reversal_of_transfer_id');
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropUnique(['reversal_of_transfer_id']);
            $table->dropColumn('reversal_of_transfer_id');
        });
    }
};
