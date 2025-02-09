<?php

namespace App\Models\Response;

class OrderDiscountResponse
{
    public function __construct(
        public string $discountType,
        public string $orderId,
        public string $productId,
        public string $productName,
        public float $discountAmount,
        public float $subTotal,
        public int $createdAt
    ) {}
}
