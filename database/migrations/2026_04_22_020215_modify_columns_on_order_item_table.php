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
        Schema::table('order__items', function (Blueprint $table) {
            $table->json('options')->nullable()->change();
            $table->string('product_name')->nullable();
            $table->string('product_image')->nullable();
            $table->integer('quantity')->default(0)->change();
             $table->decimal('price', 10, 2)->default(0.00)->change();
             $table->decimal('total', 10, 2)->default(0.00);
             $table->integer('return_quantity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order__items', function (Blueprint $table) {
            $table->longText('options')->nullable()->change();
            $table->dropColumn('product_name');
            $table->dropColumn('product_image');
            $table->integer('quantity')->nullable(false)->change();
            $table->decimal('price', 10, 2)->nullable(false)->change();
            $table->decimal('total', 10, 2)->nullable(false)->change();
            $table->dropColumn('return_quantity');
        });
    }
};
