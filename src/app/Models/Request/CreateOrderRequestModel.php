<?php

class CreateOrderRequestModel
{
    public function __construct(
        public string $order_package_id,
        public string $product_id,
        public float $quantity,
        public float $unit_price,
        public float $discount,
        public float $total
    ) {}
}
