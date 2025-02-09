<?php

namespace App\Infrastructure\Handlers\Queries;

use App\Infrastructure\Abstraction\Service\IOrderService;

class GetOrderHistoryQuery
{
    public function __construct(
        public $customerId,
        public $orderPackageId
    ) {}
}


class GetOrderHistoryQueryHandler
{

    public function __construct(private IOrderService $orderService) {}

    public function handle(GetOrderHistoryQuery $query)
    {
        return $this->orderService->getOrders($query->customerId, $query->orderPackageId);
    }
}
