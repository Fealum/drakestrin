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
            ->select(['id', 'description', 'text'])
            ->orderBy('id')
            ->chunkById(100, function ($companies): void {
                foreach ($companies as $company) {
                    $description = trim((string) $company->description);
                    $longDescription = trim((string) $company->text);

                    if ($longDescription === '') {
                        continue;
                    }

                    DB::table('companies')->where('id', $company->id)->update([
                        'description' => $description === ''
                            ? $longDescription
                            : $description."\n\n".$longDescription,
                    ]);
                }
            });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('text');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->mediumText('text')->default('');
        });
    }
};
