<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['createlocation', 'editlocation', 'deletelocation'] as $name) {
            DB::table('permits')->updateOrInsert(
                ['name' => $name],
                ['standard' => 0],
            );
        }
    }

    public function down(): void
    {
        DB::table('permits')
            ->whereIn('name', ['createlocation', 'editlocation', 'deletelocation'])
            ->delete();
    }
};
