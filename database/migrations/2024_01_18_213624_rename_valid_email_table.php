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
        Schema::rename('dra_validemail', 'valid_emails');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('valid_emails', 'dra_validemail');
    }
};
