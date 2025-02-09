<?php

namespace App\Infrastructure\Handlers;

use Illuminate\Support\Facades\App;
use ReflectionClass;

class HandlerBus
{
    public function handle($command)
    {
        $reflection = new ReflectionClass($command);
        $handlerName = $reflection->getShortName() . 'Handler';
        $handlerName = str_replace($reflection->getShortName(), $handlerName, $reflection->getName());
        $handler = App::make($handlerName);
        return $handler->handle($command);
    }
}