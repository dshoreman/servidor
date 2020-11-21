<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTables extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name');
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();
        });

        Schema::create('project_applications', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id');
            $table->string('template');
            $table->string('domain_name');
            $table->string('source_repository');
            $table->string('source_branch');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_applications');
        Schema::dropIfExists('projects');
    }
}
