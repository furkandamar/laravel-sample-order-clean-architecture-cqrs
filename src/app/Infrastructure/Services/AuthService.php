<?php

namespace App\Infrastructure\Services;

use App\Exceptions\ApiException;
use App\Infrastructure\Abstraction\Service\IAuthService;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService implements IAuthService
{
    public function login($email, $password)
    {
        try {
            if (! JWTAuth::attempt(['email' => $email, 'password' => $password])) {
                throw new ApiException('Unauthorized', 401);
            }
            $user = auth()->user();
            return JWTAuth::claims([])->fromUser($user);
        } catch (JWTException $e) {
            throw new ApiException($e->getMessage(), 500);
        }
    }

    public function me($customerId)
    {
        return auth()->user();
    }
}
