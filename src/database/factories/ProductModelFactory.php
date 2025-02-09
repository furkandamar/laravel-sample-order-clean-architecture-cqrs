<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductModelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => null,
            'category_id' => null,
            'price' => null,
            'stock' => null
        ];
    }
}