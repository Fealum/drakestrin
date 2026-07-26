<?php

use App\Support\PermissionEntityType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permits')->updateOrInsert(
            ['name' => 'exportmarkdown'],
            ['standard' => 0],
        );

        $permitId = DB::table('permits')->where('name', 'exportmarkdown')->value('id');
        $administratorGroupId = DB::table('groups')->where('name', 'Administrator')->value('id');

        if ($permitId && $administratorGroupId) {
            DB::table('permissions')->updateOrInsert(
                [
                    'recipient_type' => PermissionEntityType::GROUP->value,
                    'recipient_id' => $administratorGroupId,
                    'subject_type' => 0,
                    'subject_id' => 0,
                    'permit_id' => $permitId,
                ],
                ['value' => 2],
            );
        }

        Cache::flush();
    }

    public function down(): void
    {
        $permitId = DB::table('permits')->where('name', 'exportmarkdown')->value('id');

        if ($permitId) {
            DB::table('permissions')
                ->where('permit_id', $permitId)
                ->delete();
        }

        DB::table('permits')
            ->where('name', 'exportmarkdown')
            ->delete();

        Cache::flush();
    }
};
