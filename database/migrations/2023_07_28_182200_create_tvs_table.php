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
        Schema::create('tvs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->longText('tv_config');
            $table->longText('tv_img');
            $table->longText('tv_marquee');
            $table->timestamps();
            $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tvs');
    }
};
