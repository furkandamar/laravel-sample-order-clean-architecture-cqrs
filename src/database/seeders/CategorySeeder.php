<?php

namespace Database\Seeders;

use App\Models\CategoryModel;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        static $index = 0;
        \App\Models\CategoryModel::factory()
            ->count(3)
            ->state(function (array $attributes, $model) use (&$index) {
                $index++;
                return ['category_name' => 'Category ' . $index];
            })
            ->create();
    }
}