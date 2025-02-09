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

        Route::get("/orders/{orderPackageId?}", [OrderController::class, "getOrders"]);
        Route::post("/orders", [OrderController::class, "createOrder"]);
        Route::put("/orders/{orderPackageId}", [OrderController::class, "updateOrderPackage"]);
        Route::delete("/orders/{orderPackageId}", [OrderController::class, "cancelOrderPackage"]);
        Route::get("/discount/{orderPackageId}", [OrderController::class, "getDiscount"]);
    });
});
