<?php

<<<<<<< Updated upstream
namespace Safe;

use Safe\Exceptions\FpmException;

/**
 * This function flushes all response data to the client and finishes the
 * request. This allows for time consuming tasks to be performed without
 * leaving the connection to the client open.
 *
 * @throws FpmException
 *
 */
function fastcgi_finish_request(): void
{
    error_clear_last();
    $safeResult = \fastcgi_finish_request();
    if ($safeResult === false) {
        throw FpmException::createFromPhpError();
    }
=======
if (str_starts_with(PHP_VERSION, "8.1.") || str_starts_with(PHP_VERSION, "8.0.")) {
    require_once __DIR__ . '/8.1/fpm.php';
}
if (str_starts_with(PHP_VERSION, "8.2.")) {
    require_once __DIR__ . '/8.2/fpm.php';
}
if (str_starts_with(PHP_VERSION, "8.3.")) {
    require_once __DIR__ . '/8.2/fpm.php';
}
if (str_starts_with(PHP_VERSION, "8.4.")) {
    require_once __DIR__ . '/8.2/fpm.php';
}
if (str_starts_with(PHP_VERSION, "8.5.")) {
    require_once __DIR__ . '/8.2/fpm.php';
}
if (str_starts_with(PHP_VERSION, "8.6.")) {
    require_once __DIR__ . '/8.2/fpm.php';
>>>>>>> Stashed changes
}

