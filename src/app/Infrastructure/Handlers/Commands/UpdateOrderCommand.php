<?php

namespace App\Infrastructure\Handlers\Commands;

use App\Infrastructure\Abstraction\Service\IOrderService;

class UpdateOrderCommand
{
    public function __construct(
        public string $customerId,
        public string $orderPackageId,
        public array $products
    ) {}
}

class UpdateOrderCommandHandler
{
    public function __construct(private IOrderService $orderService) {}
    public function handle(UpdateOrderCommand $command)
    {
        return $this->orderService->updateOrderPackage($command->customerId, $command->orderPackageId, $command->products);
    }
}
