<?php

use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Helpers\ApiResponse;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    public function render($request, \Throwable $e)
    {
        if ($request->expectsJson()) {
            // Validation Errors
            if ($e instanceof ValidationException) {
                return ApiResponse::error(
                    'Validation failed',
                    422,
                    $e->errors()
                );
            }

            // Model Not Found
            if ($e instanceof ModelNotFoundException) {
                return ApiResponse::error(
                    'Resource not found',
                    404
                );
            }

            // Route Not Found
            if ($e instanceof NotFoundHttpException) {
                return ApiResponse::error(
                    'Endpoint not found',
                    404
                );
            }

            // Custom API Exception
            if ($e instanceof \App\Exceptions\ApiException) {
                return ApiResponse::error(
                    $e->getMessage(),
                    $e->getCode()
                );
            }

            // Generic Error (Prod ortamında detay gösterme)
            return ApiResponse::error(
                config('app.debug') ? $e->getMessage() : 'Internal Server Error',
                500
            );
        }

        return parent::render($request, $e);
    }
}
