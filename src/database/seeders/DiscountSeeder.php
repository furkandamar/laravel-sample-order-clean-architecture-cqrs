<?php

namespace Database\Seeders;

use App\Models\CategoryModel;
use App\Models\DiscountModel;
use App\Models\ProductModel;
use DiscountTypeConstant;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run()
    {
        $categories = CategoryModel::limit(2)->orderBy('created_at')->get();
        $firstProduct = ProductModel::first();

        DiscountModel::create(
            [
                'discount_type' => DiscountTypeConstant::TOTAL_QUANTITY_GREATER_PERCENTAGE_DISCOUNT,
                'min_limit_quantity' => 1000,
                'discount_value' => 10
            ]
        );
        DiscountModel::create([
            'discount_type' => DiscountTypeConstant::MANY_GET_ONE_FREE,
            'category_id' => $categories->last()->id,
            'min_limit_quantity' => 5,
            'discount_value' => 1
        ]);
        DiscountModel::create([
            'discount_type' => DiscountTypeConstant::TOTAL_AMOUNT_GREATER_PERCENTAGE_CHEAPEST_PRODUCT,
            'category_id' => $categories->first()->id,
            'min_limit_quantity' => 2,
            'discount_value' => 20
        ]);
        DiscountModel::create([
            'discount_type' => DiscountTypeConstant::TOTAL_AMOUNT_GREATER_PERCENTAGE_DISCOUNT,
            'product_id' => $firstProduct->id,
            'min_limit_quantity' => 5,
            'discount_value' => 20
        ]);
    }
}