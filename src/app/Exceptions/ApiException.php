<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function render($request)
    {
        if ($request->expectsJson()) {
            return \App\Helpers\ApiResponse::error(
                $this->getMessage(),
                $this->getCode() ?: 400
            );
        }

        return response()->json([
            'message' => $this->getMessage(),
            'code' => $this->getCode() ?: 400
        ], $this->getCode() ?: 400);
    }
}
