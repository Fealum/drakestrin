<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dra_company', function (Blueprint $table) {
            $table->renameColumn('territory', 'territory_id');
            $table->renameColumn('thread', 'thread_id');
        });
        Schema::rename('dra_company', 'companies');

        Schema::table('dra_company_worker', function (Blueprint $table) {
            $table->renameColumn('company', 'company_id');
        });
        Schema::rename('dra_company_worker', 'company_workers');
    }

    public function down(): void
    {
        Schema::rename('company_workers', 'dra_company_worker');
        Schema::table('dra_company_worker', function (Blueprint $table) {
            $table->renameColumn('company_id', 'company');
        });

        Schema::rename('companies', 'dra_company');
        Schema::table('dra_company', function (Blueprint $table) {
            $table->renameColumn('thread_id', 'thread');
            $table->renameColumn('territory_id', 'territory');
        });
    }
};
