<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_contacts', function (Blueprint $table) {
            $table->renameColumn('protocol', 'protocol_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_contacts', function (Blueprint $table) {
            $table->renameColumn('protocol_id', 'protocol');
        });
    }
};
