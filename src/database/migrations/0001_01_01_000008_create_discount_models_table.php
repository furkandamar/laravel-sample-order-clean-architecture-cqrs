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
        Schema::create(DbTableNameConstant::DISCOUNT, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('discount_type');
            $table->uuid('product_id')->nullable();
            $table->uuid('category_id')->nullable();
            $table->foreign('product_id')->references('id')->on(DbTableNameConstant::PRODUCT);
            $table->foreign('category_id')->references('id')->on('category');
            $table->decimal('min_limit_quantity');
            $table->decimal('discount_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DbTableNameConstant::DISCOUNT);
    }
};
