<?php

namespace App\Infrastructure\Handlers\Queries;

use App\Infrastructure\Abstraction\Service\IOrderService;
use Illuminate\Support\Facades\Cache;

class GetDiscountQuery
{
    public function __construct(public $customerId, public $orderPackageId) {}
}

class GetDiscountQueryHandler
{
    public function __construct(private IOrderService $orderService) {}

    public function handle(GetDiscountQuery $query)
    {
        return $this->orderService->getDiscount($query->customerId, $query->orderPackageId);
    }
}
