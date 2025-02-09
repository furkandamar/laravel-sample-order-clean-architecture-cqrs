<?php

namespace App\Infrastructure\Handlers\Commands;

use App\Infrastructure\Abstraction\Service\IOrderService;

class CancelOrderCommand
{
    public function __construct(
        public string $customerId,
        public string $orderPackageId
    ) {}
}

class CancelOrderCommandHandler
{
    public function __construct(
        private IOrderService $orderService
    ) {}

    public function handle(CancelOrderCommand $command)
    {
        $this->orderService->cancelOrderPackage($command->customerId, $command->orderPackageId);
    }
}
