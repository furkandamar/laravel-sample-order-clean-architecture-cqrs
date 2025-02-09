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
        Schema::create(DbTableNameConstant::ORDER, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_package_id');
            $table->foreign('order_package_id')->references('id')->on(DbTableNameConstant::ORDER_PACKAGE);
            $table->uuid('product_id');
            $table->foreign('product_id')->references('id')->on('product');
            $table->decimal('quantity');
            $table->decimal('unit_price');
            $table->decimal('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DbTableNameConstant::ORDER);
    }
};
