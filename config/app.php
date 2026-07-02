<?php

return [
    'name'    => env('APP_NAME', 'MyApp'),
    'env'     => env('APP_ENV', 'production'),
    'debug'   => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    'url'     => env('APP_URL', 'http://localhost'),
    'booking_quota' => (int) env('SERVICE_BOOKING_QUOTA', 5),
];
