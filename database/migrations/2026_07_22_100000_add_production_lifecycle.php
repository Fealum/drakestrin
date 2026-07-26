<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labour_actives', function (Blueprint $table) {
            $table->bigInteger('stop_requested_at')->nullable()->after('nextinsta');
            $table->bigInteger('ended_at')->nullable()->after('stop_requested_at');
            $table->json('input_items')->nullable()->after('ended_at');
            $table->json('output_items')->nullable()->after('input_items');
            $table->json('tool_items')->nullable()->after('output_items');
            $table->index(['ended_at', 'until']);
        });

        Schema::create('production_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('labour_active_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('company_worker_id');
            $table->unsignedBigInteger('labour_id');
            $table->string('labour_name');
            $table->string('worker_name');
            $table->unsignedInteger('instances');
            $table->integer('output_state');
            $table->json('inputs');
            $table->json('outputs');
            $table->bigInteger('started_at');
            $table->bigInteger('due_at');
            $table->bigInteger('completed_at')->nullable();

            $table->index(['labour_active_id', 'completed_at']);
            $table->index(['company_id', 'completed_at']);
            $table->index(['company_worker_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_runs');

        Schema::table('labour_actives', function (Blueprint $table) {
            $table->dropIndex(['ended_at', 'until']);
            $table->dropColumn([
                'stop_requested_at',
                'ended_at',
                'input_items',
                'output_items',
                'tool_items',
            ]);
        });
    }
};
