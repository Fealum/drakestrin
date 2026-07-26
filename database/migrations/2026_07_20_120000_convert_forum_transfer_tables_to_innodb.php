<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = [
        'boards',
        'threads',
        'posts',
        'users',
        'characters',
        'inventories',
        'transfers',
        'transfer_items',
    ];

    public function up(): void
    {
        $this->setEngine('InnoDB');
    }

    public function down(): void
    {
        $this->setEngine('MyISAM');
    }

    private function setEngine(string $engine): void
    {
        foreach (self::TABLES as $table) {
            if (Schema::hasTable($table)) {
                DB::statement(sprintf('ALTER TABLE `%s` ENGINE=%s', $table, $engine));
            }
        }
    }
};
