<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LogRequestResponse;
use Illuminate\Support\Facades\Route;

Route::middleware([LogRequestResponse::class])->group(function () {

    Route::post("/login", [AuthController::class, "login"]);

    Route::get("/products", [ProductController::class, "getProducts"]);
    Route::get("/categories", [ProductController::class, "getCategories"]);

    Route::middleware([JwtMiddleware::class])->group(function () {
        Route::get("/me", [AuthController::class, "me"]);


        Route::get("/orders", [OrderController::class, "getOrders"]);
        Route::post("/orders", [OrderController::class, "createOrder"]);

        Route::get("/discount/{order_package_id}", [OrderController::class, "getDiscount"]);
    });
});
