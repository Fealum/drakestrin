<?php

use App\Services\Board\PostElementMigrationAuditor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $report = app(PostElementMigrationAuditor::class)->audit();
        if (! $report['passed']) {
            throw new RuntimeException('Legacy post cleanup refused: '.json_encode($report['issues'], JSON_THROW_ON_ERROR));
        }

        // Verify everything before the first DDL statement: MySQL cannot roll it back.
        foreach ([
            'posts' => ['message', 'smilies', 'signature'],
            'characters' => ['signature'],
            'users' => ['hide_signature'],
            'bb3_config' => ['ch_signature'],
        ] as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $columns = array_values(array_intersect($columns, Schema::getColumnListing($table)));
            if ($columns !== []) {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn($columns));
            }
        }
    }

    public function down(): void
    {
        throw new LogicException('This cleanup is irreversible. Restore the verified database backup to recover legacy signature preferences.');
    }
};
