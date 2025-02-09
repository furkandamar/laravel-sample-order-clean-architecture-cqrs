<?php

namespace App\Infrastructure\Services;

use App\Events\ProductCreatedEvent;
use App\Infrastructure\Events\RequestLogEvent;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class EventListenerService
{
    public function handle(RequestLogEvent $event)
    {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST'),
            env('RABBITMQ_PORT'),
            env('RABBITMQ_USER'),
            env('RABBITMQ_PASS')
        );

        $channel = $connection->channel();
        $channel->queue_declare('request', false, true, false, false);

        $message = json_encode([
            'request_id' => $event->log->id,
            'url' => $event->log->url
        ]);

        $channel->basic_publish(new \PhpAmqpLib\Message\AMQPMessage($message), '', 'request');
        $channel->close();
        $connection->close();
    }
}
