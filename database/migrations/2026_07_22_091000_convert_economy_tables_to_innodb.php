<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = [
        'companies',
        'company_workers',
        'labours',
        'labour_components',
        'labour_actives',
        'items',
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
