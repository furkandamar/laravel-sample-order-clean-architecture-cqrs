<?php

namespace App\Infrastructure\Handlers\Commands;

use App\Infrastructure\Abstraction\Service\IAuthService;

class GetMeQuery
{
    public function __construct(
        public string $customerId,
    ) {}
}

class GetMeQueryHandler
{
    public function __construct(
        private IAuthService $authService
    ) {}

    public function handle(GetMeQuery $query)
    {
        return $this->authService->me($query->customerId);
    }
}
