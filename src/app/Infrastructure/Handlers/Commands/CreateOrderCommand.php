<?php

namespace App\Infrastructure\Handlers\Commands;

use App\Infrastructure\Abstraction\Service\IOrderService;

class CreateOrderCommand
{
    public function __construct(
        public string $customerId,
        public array $products
    ) {}
}

class CreateOrderCommandHandler
{
    public function __construct(private IOrderService $orderService) {}
    public function handle(CreateOrderCommand $command)
    {
        return $this->orderService->createOrder($command->customerId, $command->products);
    }
}
