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
        Schema::create('sensor_reports', function (Blueprint $table) {
            $table->id();
            $table->float('tinggi_air'); 
            $table->float('ph');        
            $table->float('debit');
            $table->enum('status', ['normal', 'warning', 'critical']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_reports');
    }
};
