<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class EventLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'event_logs';
    protected $fillable = [
        'request_method',
        'request_url',
        'request_body',
        'response_status',
        'response_body',
        'execution_time',
        'created_at'
    ];
}
