<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labour_actives', function (Blueprint $table) {
            $table->bigInteger('paused_at')->nullable()->after('stop_requested_at');
            $table->string('pause_reason', 32)->nullable()->after('paused_at');
            $table->index(['ended_at', 'paused_at']);
        });
    }

    public function down(): void
    {
        Schema::table('labour_actives', function (Blueprint $table) {
            $table->dropIndex(['ended_at', 'paused_at']);
            $table->dropColumn(['paused_at', 'pause_reason']);
        });
    }
};
