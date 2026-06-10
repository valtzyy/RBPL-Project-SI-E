<?php

return [
    'name'    => $_ENV['APP_NAME']  ?? 'MyApp',
    'env'     => $_ENV['APP_ENV']   ?? 'production',
    'debug'   => $_ENV['APP_DEBUG'] ?? false,
    'url'     => $_ENV['APP_URL']   ?? 'http://localhost',
];