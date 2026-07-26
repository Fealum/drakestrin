<?php

use App\Support\PermissionEntityType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permitNames = ['createlocation', 'editlocation', 'deletelocation', 'setthreadscene', 'endthreadscene'];
        $permitIds = DB::table('permits')
            ->whereIn('name', $permitNames)
            ->pluck('id', 'name');

        foreach ($permitIds as $permitId) {
            DB::table('permissions')->updateOrInsert(
                [
                    'recipient_type' => PermissionEntityType::USER->value,
                    'recipient_id' => 37,
                    'subject_type' => 0,
                    'subject_id' => 0,
                    'permit_id' => $permitId,
                ],
                ['value' => 2],
            );
        }

        Cache::forget('user_permits:37');
        Cache::forget('user_permissions:37');
    }

    public function down(): void
    {
        $permitIds = DB::table('permits')
            ->whereIn('name', ['createlocation', 'editlocation', 'deletelocation', 'setthreadscene', 'endthreadscene'])
            ->pluck('id');

        DB::table('permissions')
            ->where('recipient_type', PermissionEntityType::USER->value)
            ->where('recipient_id', 37)
            ->where('subject_type', 0)
            ->where('subject_id', 0)
            ->whereIn('permit_id', $permitIds)
            ->delete();

        Cache::forget('user_permits:37');
        Cache::forget('user_permissions:37');
    }
};
