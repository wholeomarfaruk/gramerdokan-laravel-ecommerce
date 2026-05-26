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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('subtotal')->default(0);
            $table->decimal('discount')->default(0);
            $table->decimal('total')->default(0);
            $table->decimal('cod_charge')->default(0);
            $table->decimal('cod_percentage')->default(0);
            $table->decimal('fee')->default(0);
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('delivery_area_id')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('is_paid')->default(false);
            $table->date('delivery_date')->nullable();
            $table->date('cancelled_date')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('notes')->nullable();
            $table->json('json_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
