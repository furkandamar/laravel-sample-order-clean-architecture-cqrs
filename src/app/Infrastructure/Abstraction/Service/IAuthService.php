<?php

namespace App\Infrastructure\Abstraction\Service;

interface IAuthService
{
    public function login($email, $password);
    public function me($customerId);
}
