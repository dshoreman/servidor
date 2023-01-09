<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfigToProjectApplications extends Migration
{
    public function up(): void
    {
        Schema::table('project_applications', static function (Blueprint $table): void {
            $table->json('config')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('project_applications', static function (Blueprint $table): void {
            $table->dropColumn('config');
        });
    }
}
