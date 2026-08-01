<?php

use App\Support\PermissionEntityType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_sites', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable()->change();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('headquarters_site_id')->nullable()->after('created_by_user_id');
        });

        Schema::create('company_owners', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('character_id');
            $table->unsignedBigInteger('added_by_user_id')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'character_id']);
            $table->index('character_id');
        });

        Schema::table('company_representatives', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'character_id']);
            $table->unsignedBigInteger('company_site_id')->nullable()->after('company_id');
            $table->index(['company_site_id', 'role']);
        });

        Schema::create('company_role_events', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->unsignedBigInteger('company_site_id')->nullable();
            $table->integer('character_id');
            $table->string('role', 30);
            $table->string('action', 30);
            $table->unsignedBigInteger('acted_by_user_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['company_id', 'created_at']);
            $table->index(['character_id', 'role']);
        });

        Schema::table('company_workers', function (Blueprint $table) {
            $table->unsignedBigInteger('company_site_id')->nullable()->after('company_id');
            $table->index(['company_site_id', 'paid']);
        });

        Schema::table('production_runs', function (Blueprint $table) {
            $table->unsignedBigInteger('company_site_id')->nullable()->after('company_id');
            $table->index(['company_site_id', 'completed_at']);
        });

        $companyType = PermissionEntityType::COMPANY->value;
        $siteType = PermissionEntityType::COMPANY_SITE->value;

        DB::table('companies')->orderBy('id')->each(function (object $company) use ($companyType, $siteType) {
            $site = DB::table('company_sites')
                ->where('company_id', $company->id)
                ->orderByDesc('is_headquarters')
                ->orderBy('id')
                ->first();
            $siteId = $site ? (int) $site->id : DB::table('company_sites')->insertGetId([
                'company_id' => $company->id,
                'location_id' => null,
                'name' => 'Hauptstandort',
                'is_headquarters' => true,
                'is_storefront' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('company_sites')->where('id', $siteId)->update([
                'name' => $site?->name ?: 'Hauptstandort',
                'is_headquarters' => true,
            ]);
            DB::table('companies')->where('id', $company->id)->update(['headquarters_site_id' => $siteId]);

            if ($company->character_id) {
                DB::table('company_owners')->insertOrIgnore([
                    'company_id' => $company->id,
                    'character_id' => $company->character_id,
                    'added_by_user_id' => $company->created_by_user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('inventories')->where('owner_type', $companyType)->where('owner_id', $company->id)
                ->update(['owner_type' => $siteType, 'owner_id' => $siteId]);
            DB::table('transfers')->where('sender_type', $companyType)->where('sender_id', $company->id)
                ->update(['sender_type' => $siteType, 'sender_id' => $siteId]);
            DB::table('transfers')->where('recipient_type', $companyType)->where('recipient_id', $company->id)
                ->update(['recipient_type' => $siteType, 'recipient_id' => $siteId]);
            DB::table('company_workers')->where('company_id', $company->id)->update(['company_site_id' => $siteId]);
            DB::table('production_runs')->where('company_id', $company->id)->update(['company_site_id' => $siteId]);

            DB::table('inventory_mutations')->orderBy('id')->each(function (object $mutation) use ($company, $companyType, $siteId, $siteType) {
                $changes = [];

                foreach (['before_state', 'after_state'] as $column) {
                    $state = $mutation->{$column} ? json_decode($mutation->{$column}, true) : null;

                    if (is_array($state)
                        && (int) ($state['owner_type'] ?? -1) === $companyType
                        && (int) ($state['owner_id'] ?? -1) === (int) $company->id) {
                        $state['owner_type'] = $siteType;
                        $state['owner_id'] = $siteId;
                        $changes[$column] = json_encode($state);
                    }
                }

                if ($changes !== []) {
                    DB::table('inventory_mutations')->where('id', $mutation->id)->update($changes);
                }
            });
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->foreign('headquarters_site_id')->references('id')->on('company_sites')->restrictOnDelete();
        });

        Schema::table('company_sites', function (Blueprint $table) {
            $table->dropIndex(['location_id', 'is_storefront']);
            $table->dropColumn(['is_headquarters', 'is_storefront']);
            $table->index('location_id');
        });
    }

    public function down(): void
    {
        Schema::table('company_sites', function (Blueprint $table) {
            $table->boolean('is_headquarters')->default(false);
            $table->boolean('is_storefront')->default(false);
        });
        DB::table('companies')->whereNotNull('headquarters_site_id')->each(function (object $company) {
            DB::table('company_sites')->where('id', $company->headquarters_site_id)->update(['is_headquarters' => true]);
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['headquarters_site_id']);
            $table->dropColumn('headquarters_site_id');
        });
        Schema::table('production_runs', function (Blueprint $table) {
            $table->dropIndex(['company_site_id', 'completed_at']);
            $table->dropColumn('company_site_id');
        });
        Schema::table('company_workers', function (Blueprint $table) {
            $table->dropIndex(['company_site_id', 'paid']);
            $table->dropColumn('company_site_id');
        });
        Schema::dropIfExists('company_role_events');
        Schema::table('company_representatives', function (Blueprint $table) {
            $table->dropIndex(['company_site_id', 'role']);
            $table->dropColumn('company_site_id');
            $table->unique(['company_id', 'character_id']);
        });
        Schema::dropIfExists('company_owners');
        Schema::table('company_sites', function (Blueprint $table) {
            $table->index(['location_id', 'is_storefront']);
        });
    }
};
