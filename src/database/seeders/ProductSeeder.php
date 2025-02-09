<?php

namespace Database\Seeders;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        static $index = 0;
        $categories = CategoryModel::all();

        \App\Models\ProductModel::factory()
            ->count(10)
            ->state(function (array $attributes, $model) use ($categories, &$index) {
                $index++;
                return [
                    'name' => 'Product ' . $index,
                    'category_id' => $categories->random()->id,
                    'price' => random_int(1, 30),
                    'stock' => random_int(10, 100)
                ];
            })
            ->create();
    }
}
