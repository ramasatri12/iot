<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sensor_reports', function (Blueprint $table) {
            if (Schema::hasColumn('sensor_reports', 'ph')) {
                $table->dropColumn('ph');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensor_reports', function (Blueprint $table) {
            $table->float('ph')->after('tinggi_air')->nullable();

        });
    }
};
