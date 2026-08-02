<?php

use App\Support\PermissionEntityType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const LAST_LEGACY_POST_ID = 50148;

    public function up(): void
    {
        $this->invertLegacySmileyFlag();

        $permitId = DB::table('permits')->where('name', 'viewip')->value('id');
        $administratorGroupId = DB::table('groups')->where('name', 'Administrator')->value('id');

        if ($permitId && $administratorGroupId) {
            DB::table('permissions')->updateOrInsert([
                'recipient_type' => PermissionEntityType::GROUP->value,
                'recipient_id' => $administratorGroupId,
                'subject_type' => 0,
                'subject_id' => 0,
                'permit_id' => $permitId,
            ], ['value' => 2]);
        }

        Cache::flush();
    }

    public function down(): void
    {
        $permitId = DB::table('permits')->where('name', 'viewip')->value('id');
        $administratorGroupId = DB::table('groups')->where('name', 'Administrator')->value('id');

        if ($permitId && $administratorGroupId) {
            DB::table('permissions')->where([
                'recipient_type' => PermissionEntityType::GROUP->value,
                'recipient_id' => $administratorGroupId,
                'subject_type' => 0,
                'subject_id' => 0,
                'permit_id' => $permitId,
            ])->delete();
        }

        $this->invertLegacySmileyFlag();
        Cache::flush();
    }

    private function invertLegacySmileyFlag(): void
    {
        DB::table('posts')
            ->where('id', '<=', self::LAST_LEGACY_POST_ID)
            ->update(['smilies' => DB::raw('1 - smilies')]);
    }
};
