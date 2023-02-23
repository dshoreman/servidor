<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MergeApplicationsAndRedirectsToServicesTable extends Migration
{
    public function up(): void
    {
        Schema::table('project_applications', static function (Blueprint $table): void {
            $table->dropColumn([
                'source_branch',
                'source_provider',
                'source_repository',
            ]);
        });

        Schema::table('project_redirects', static function (Blueprint $table): void {
            $table->dropColumn([
                'target',
                'type',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('project_applications', static function (Blueprint $table): void {
            $table->string('source_provider')->default('');
            $table->string('source_repository')->default('');
            $table->string('source_branch')->default('');
        });

        Schema::table('project_redirects', static function (Blueprint $table): void {
            $table->string('target');
            $table->smallInteger('type');
        });
    }
}
