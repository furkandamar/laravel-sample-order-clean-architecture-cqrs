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
        Schema::create(DbTableNameConstant::ORDER_DISCOUNT, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->foreign('order_id')->references('id')->on(DbTableNameConstant::ORDER);
            $table->uuid('discount_id');
            $table->foreign('discount_id')->references('id')->on(DbTableNameConstant::DISCOUNT);
            $table->decimal('discount_amount');
            $table->decimal('sub_total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DbTableNameConstant::ORDER_DISCOUNT);
    }
};
