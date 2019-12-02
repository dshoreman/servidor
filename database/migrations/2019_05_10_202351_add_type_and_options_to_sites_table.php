<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeAndOptionsToSitesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $types = [
            'basic',
            'php',
            'laravel',
            'redirect',
        ];

        Schema::table('sites', function (Blueprint $table) use ($types) {
            $table->enum('type', $types)->nullable();
            $table->string('source_repo')->nullable();
            $table->string('document_root')->nullable();
            $table->integer('redirect_type')->nullable();
            $table->string('redirect_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn([
                'redirect_to',
                'redirect_type',
                'document_root',
                'source_repo',
                'type',
            ]);
        });
    }
}
