<?php

namespace App\Http\Middleware;

use App\Infrastructure\Events\RequestLogEvent;
use App\Models\EventLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Symfony\Component\HttpFoundation\Response;

class LogRequestResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        try {
            $response = $next($request);
        } catch (\Exception $e) {
            $response = response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        $this->logToMongo($request, $response, $duration);

        return $response;
    }

    private function logToMongo(Request $request, Response $response, $duration)
    {
        $log = new EventLog();
        $log->request_method = $request->method();
        $log->request_url = $request->fullUrl();
        $log->request_body = $request->except(['password', 'password_confirmation']);
        $log->response_status = $response->status();
        $log->response_body = json_decode($response->getContent(), true);
        $log->execution_time = $duration;
        $log->created_at = now();
        event(new RequestLogEvent($log));
    }
}
