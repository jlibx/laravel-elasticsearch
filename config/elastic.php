<?php

use Illuminate\Support\Str;

return [
    'prefix' => env('ELASTIC_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_')),
    'hosts' => [
        env('ELASTIC_HOST', 'elasticsearch:9200'),
    ],
    'queue' => env('ELASTIC_QUEUE', false),
    'after_commit' => false,
    'chunk' => 500,
    'log' => env('ELASTIC_LOG', true)
];
