<?php

namespace App\Infrastructure\Handlers\Commands;

use App\Models\EventLog;

class CreateLogEventCommand
{
    public function handle(EventLog $event)
    {
        RabbitMQ::publish(
            'user_created',
            json_encode($event->user)
        );
    }
}
