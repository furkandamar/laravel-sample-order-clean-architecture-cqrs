<?php

namespace App\Infrastructure\Events;

use App\Models\EventLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public EventLog $log) {}
}
