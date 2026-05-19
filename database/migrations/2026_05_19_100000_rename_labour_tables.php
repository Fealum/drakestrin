<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('dra_labour', 'labours');

        Schema::table('dra_labour_component', function (Blueprint $table) {
            $table->renameColumn('labour', 'labour_id');
        });
        Schema::rename('dra_labour_component', 'labour_components');

        Schema::table('dra_labour_active', function (Blueprint $table) {
            $table->renameColumn('company_worker', 'company_worker_id');
            $table->renameColumn('labour', 'labour_id');
        });
        Schema::rename('dra_labour_active', 'labour_actives');
    }

    public function down(): void
    {
        Schema::rename('labour_actives', 'dra_labour_active');
        Schema::table('dra_labour_active', function (Blueprint $table) {
            $table->renameColumn('labour_id', 'labour');
            $table->renameColumn('company_worker_id', 'company_worker');
        });

        Schema::rename('labour_components', 'dra_labour_component');
        Schema::table('dra_labour_component', function (Blueprint $table) {
            $table->renameColumn('labour_id', 'labour');
        });

        Schema::rename('labours', 'dra_labour');
    }
};
