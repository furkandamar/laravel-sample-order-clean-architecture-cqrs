<?php

namespace App\Infrastructure\Abstraction\Service;

interface IOrderService
{
    public function createOrder($customerId, $products);
    public function getOrders($customerId, $orderPackageId = null);
    public function getDiscount($orderPackageId);
}
