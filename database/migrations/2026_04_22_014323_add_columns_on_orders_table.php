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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('payment_method')->default('cod');
            $table->string('transaction_id')->nullable();
            $table->string('payment_status');
            $table->decimal('grand_total', 10, 2)->default(0.00);

            $table->index(['status', 'payment_status'], 'orders_status_payment_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('customer_id');
            $table->dropColumn('device_id');
            $table->dropColumn('email');
            $table->dropColumn('payment_method');
            $table->dropColumn('transaction_id');
            $table->dropColumn('payment_status');
            $table->dropColumn('grand_total');
        });
    }
};
