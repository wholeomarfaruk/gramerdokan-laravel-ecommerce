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
        // Create pivot table for segments and segmentable models
        Schema::create('segmentables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('segment_id');
            $table->morphs('segmentable');
            $table->timestamps();
            $table->foreign('segment_id')->references('id')->on('segments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segmentables');
    }
};
