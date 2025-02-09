<?php

namespace App\Models\Response;

class OrderProductItemResponse
{
    public function __construct(
        public string $orderId,
        public string $productId,
        public string $productName,
        public float $quantity,
        public float $unitPrice,
        public float $total
    ) {}
}
