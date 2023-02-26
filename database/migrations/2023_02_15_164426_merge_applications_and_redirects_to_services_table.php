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

        Schema::rename('project_applications', 'project_services');
        Schema::dropIfExists('project_redirects');
    }

    public function down(): void
    {
        Schema::rename('project_services', 'project_applications');

        Schema::table('project_applications', static function (Blueprint $table): void {
            $table->string('source_provider')->default('');
            $table->string('source_repository')->default('');
            $table->string('source_branch')->default('');
        });

        Schema::create('project_redirects', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id');
            $table->string('domain_name');
            $table->boolean('include_www')->default(false);
            $table->json('config')->nullable();
            $table->string('target');
            $table->smallInteger('type');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects');
        });
    }
}
