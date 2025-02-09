<?php

namespace App\Models\Response;

class ProductResponse
{
    public function __construct(
        public string $productId,
        public string $productName,
        public string $categoryId,
        public string $categoryName,
        public float $price,
        public float $stock
    ) {}
}
