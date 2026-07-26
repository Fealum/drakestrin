<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dra_permission', function (Blueprint $table) {
            $table->renameColumn('table__recipient', 'recipient_type');
            $table->renameColumn('recipient', 'recipient_id');
            $table->renameColumn('table__subject', 'subject_type');
            $table->renameColumn('subject', 'subject_id');
            $table->renameColumn('permit', 'permit_id');
        });
    }

    public function down(): void
    {
        Schema::table('dra_permission', function (Blueprint $table) {
            $table->renameColumn('recipient_type', 'table__recipient');
            $table->renameColumn('recipient_id', 'recipient');
            $table->renameColumn('subject_type', 'table__subject');
            $table->renameColumn('subject_id', 'subject');
            $table->renameColumn('permit_id', 'permit');
        });
    }
};
