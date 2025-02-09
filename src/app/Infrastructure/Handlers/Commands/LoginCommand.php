<?php

namespace App\Infrastructure\Handlers\Commands;

use App\Infrastructure\Abstraction\Service\IAuthService;

class LoginCommand
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}

class LoginCommandHandler
{
    public function __construct(
        private IAuthService $authService
    ) {}

    public function handle(LoginCommand $command)
    {
        return $this->authService->login($command->email, $command->password);
    }
}
