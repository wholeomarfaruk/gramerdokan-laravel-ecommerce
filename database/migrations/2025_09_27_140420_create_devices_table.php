<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('device_id',255)->unique();
            $table->text('device_type')->nullable();
            $table->text('device_model')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('screen_size')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();

            // FK with customers table
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
