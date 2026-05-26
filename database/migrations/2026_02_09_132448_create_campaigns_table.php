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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); //product page 01
            $table->string('slug')->unique();
            $table->unsignedBigInteger('landing_page_id');
            $table->string('view_file')->nullable(); //templates.landing-page-01
            $table->boolean('status')->default(1);
            $table->json('json_data')->nullable();
            $table->string('landing_page_version')->nullable();
            $table->boolean('needs_update')->default(0);
            $table->timestamps();
            $table->index('slug');
            $table->foreign('landing_page_id')->references('id')->on('landing_pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
