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
        Schema::table('order_drafts', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('device_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_drafts', function (Blueprint $table) {
            $table->dropColumn('customer_id');
        });
    }
};
