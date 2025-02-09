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

        return response()->view('errors.default', ['exception' => $this], 500);
    }
}
