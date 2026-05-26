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
        Schema::table('tracking_events', function (Blueprint $table) {
             $table->text('ip_address')->nullable()->change();
            $table->text('user_agent')->nullable()->change();
            $table->text('url')->nullable()->comment('orderPage URL')->change();
            $table->text('referrer')->nullable()->comment('orderPage Referrer')->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracking_events', function (Blueprint $table) {
        });
    }
};
