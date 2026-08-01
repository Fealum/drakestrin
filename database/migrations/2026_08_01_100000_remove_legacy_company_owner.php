<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('companies')
            ->whereNotNull('character_id')
            ->orderBy('id')
            ->each(function (object $company) {
                DB::table('company_owners')->insertOrIgnore([
                    'company_id' => $company->id,
                    'character_id' => $company->character_id,
                    'added_by_user_id' => $company->created_by_user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('character_id');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->integer('character_id')->nullable()->after('type');
        });

        DB::table('companies')->orderBy('id')->each(function (object $company) {
            DB::table('companies')->where('id', $company->id)->update([
                'character_id' => DB::table('company_owners')
                    ->where('company_id', $company->id)
                    ->orderBy('id')
                    ->value('character_id'),
            ]);
        });
    }
};
