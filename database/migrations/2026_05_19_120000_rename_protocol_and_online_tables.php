<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('dra_protocol', 'protocols');
        Schema::rename('dra_online', 'onlines');
    }

    public function down(): void
    {
        Schema::rename('onlines', 'dra_online');
        Schema::rename('protocols', 'dra_protocol');
    }
};
