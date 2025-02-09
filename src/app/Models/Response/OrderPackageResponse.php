<?php

namespace App\Models\Response;

class OrderPackageResponse
{
    public function __construct(
        public string $orderPackageId,
        public int $productCount,
        public float $discount,
        public float $totalPrice,
        public int $createdAt
    ) {}
}
