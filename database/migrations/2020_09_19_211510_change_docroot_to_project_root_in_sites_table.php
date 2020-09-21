<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDocrootToProjectRootInSitesTable extends Migration
{
    public function __construct()
    {
        // This is required because the change operations in this
        // migration will otherwise fail with an exception caused
        // by the lack of an enum type in DBAL's MySQLPlatform.
        //
        // Future migrations with changes should work without
        // issue as long as this snippet is kept in-tact.
        Schema::getConnection()->getDoctrineSchemaManager()
                               ->getDatabasePlatform()
                               ->registerDoctrineTypeMapping('enum', 'string');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table): void {
            $table->renameColumn('document_root', 'project_root');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table): void {
            $table->renameColumn('project_root', 'document_root');
        });
    }
}
