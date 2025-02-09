<?php

namespace App\Http\Controllers;

use App\Infrastructure\Abstraction\Service\IOrderService;
use App\Infrastructure\Handlers\Commands\CreateOrderCommand;
use App\Infrastructure\Handlers\Commands\CreateOrderCommandHandler;
use App\Infrastructure\Handlers\HandlerBus;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthorizeController
{
    protected $customer;
    public function __construct()
    {
        try {
            $this->customer = JWTAuth::parseToken()->authenticate();
            dd($this->customer);
            if (!$this->customer) {
                return response()->json(['error' => 'Customer not found'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid token'], 400);
        }
    }
}
