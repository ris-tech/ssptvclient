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
        Schema::create('slide_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('tv_id');
            $table->unsignedBigInteger('slide_id');
            $table->integer('sorting');
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('locations');
            $table->foreign('tv_id')->references('id')->on('tvs');
            $table->foreign('slide_id')->references('id')->on('slides');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slide_images');
    }
};
