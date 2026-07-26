<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_user_id')->nullable()->after('character_id');
            $table->timestamps();
        });

        Schema::create('company_sites', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->unsignedBigInteger('location_id');
            $table->string('name')->nullable();
            $table->boolean('is_headquarters')->default(false);
            $table->boolean('is_storefront')->default(false);
            $table->timestamps();

            $table->unique(['company_id', 'location_id']);
            $table->index(['location_id', 'is_storefront']);
        });

        Schema::create('company_representatives', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('character_id');
            $table->string('role', 30)->default('manager');
            $table->unsignedBigInteger('appointed_by_user_id')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'character_id']);
            $table->index(['character_id', 'role']);
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->integer('acted_by_character_id')->nullable()->after('created_by_user_id');
            $table->index('acted_by_character_id');
        });

        foreach (['createcompany', 'editcompany', 'representcompany'] as $name) {
            DB::table('permits')->updateOrInsert(
                ['name' => $name],
                ['standard' => 1],
            );
        }
    }

    public function down(): void
    {
        DB::table('permits')
            ->whereIn('name', ['createcompany', 'editcompany', 'representcompany'])
            ->delete();

        Schema::table('transfers', function (Blueprint $table) {
            $table->dropIndex(['acted_by_character_id']);
            $table->dropColumn('acted_by_character_id');
        });

        Schema::dropIfExists('company_representatives');
        Schema::dropIfExists('company_sites');

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['created_by_user_id', 'created_at', 'updated_at']);
        });
    }
};
