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
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('event_name')->nullable();
            $table->text('tud_id')->nullable()->comment('Tracker User Device ID');
            $table->text('tracking_id')->nullable();
            $table->text('user_source_name')->nullable();
            $table->tinyInteger('is_fired')->default(0)->comment('0 = Not Fired, 1 = Fired');
            $table->timestamp('event_fired_time')->nullable();
            $table->text('campaign_id')->nullable();
            $table->text('ad_id')->nullable();
            $table->text('adset_id')->nullable();
            $table->json('json_data')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable()->comment('orderPage URL');
            $table->string('referrer')->nullable()->comment('orderPage Referrer');
            $table->string('segment')->nullable();

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
    }
};
