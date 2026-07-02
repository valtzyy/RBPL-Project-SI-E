<?php

<<<<<<< Updated upstream
namespace Safe;

use Safe\Exceptions\XmlrpcException;

/**
 * Sets xmlrpc type, base64 or datetime, for a PHP string value.
 *
 * @param string|\DateTime $value Value to set the type
 * @param string $type 'base64' or 'datetime'
 * @throws XmlrpcException
 *
 */
function xmlrpc_set_type(&$value, string $type): void
{
    error_clear_last();
    $safeResult = \xmlrpc_set_type($value, $type);
    if ($safeResult === false) {
        throw XmlrpcException::createFromPhpError();
    }
=======
if (str_starts_with(PHP_VERSION, "8.1.") || str_starts_with(PHP_VERSION, "8.0.")) {
    require_once __DIR__ . '/8.1/xmlrpc.php';
}
if (str_starts_with(PHP_VERSION, "8.2.")) {
    require_once __DIR__ . '/8.1/xmlrpc.php';
}
if (str_starts_with(PHP_VERSION, "8.3.")) {
    require_once __DIR__ . '/8.1/xmlrpc.php';
}
if (str_starts_with(PHP_VERSION, "8.4.")) {
    require_once __DIR__ . '/8.1/xmlrpc.php';
}
if (str_starts_with(PHP_VERSION, "8.5.")) {
    require_once __DIR__ . '/8.1/xmlrpc.php';
}
if (str_starts_with(PHP_VERSION, "8.6.")) {
    require_once __DIR__ . '/8.1/xmlrpc.php';
>>>>>>> Stashed changes
}

