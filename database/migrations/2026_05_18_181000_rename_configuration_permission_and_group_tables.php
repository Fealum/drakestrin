<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('dra_configuration', 'configurations');
        Schema::table('configurations', function (Blueprint $table) {
            $table->renameColumn('table__recipient', 'recipient_type');
            $table->renameColumn('recipient', 'recipient_id');
            $table->renameColumn('table__subject', 'subject_type');
            $table->renameColumn('subject', 'subject_id');
        });

        Schema::rename('dra_permit', 'permits');
        Schema::rename('dra_permission', 'permissions');

        Schema::table('dra_group', function (Blueprint $table) {
            $table->renameColumn('group__parent', 'parent_id');
        });
        Schema::rename('dra_group', 'groups');

        Schema::rename('dra_group2user', 'group_user');
        Schema::table('group_user', function (Blueprint $table) {
            $table->renameColumn('group', 'group_id');
            $table->renameColumn('user', 'user_id');
        });
    }

    public function down(): void
    {
        Schema::table('group_user', function (Blueprint $table) {
            $table->renameColumn('group_id', 'group');
            $table->renameColumn('user_id', 'user');
        });
        Schema::rename('group_user', 'dra_group2user');

        Schema::rename('groups', 'dra_group');
        Schema::table('dra_group', function (Blueprint $table) {
            $table->renameColumn('parent_id', 'group__parent');
        });

        Schema::rename('permissions', 'dra_permission');
        Schema::rename('permits', 'dra_permit');

        Schema::table('configurations', function (Blueprint $table) {
            $table->renameColumn('recipient_type', 'table__recipient');
            $table->renameColumn('recipient_id', 'recipient');
            $table->renameColumn('subject_type', 'table__subject');
            $table->renameColumn('subject_id', 'subject');
        });
        Schema::rename('configurations', 'dra_configuration');
    }
};
