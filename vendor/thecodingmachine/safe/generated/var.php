<?php

<<<<<<< Updated upstream
namespace Safe;

use Safe\Exceptions\VarException;

/**
 * Set the type of variable var to
 * type.
 *
 * @param mixed $var The variable being converted.
 * @param string $type Possibles values of type are:
 *
 *
 *
 * "boolean" or "bool"
 *
 *
 *
 *
 * "integer" or "int"
 *
 *
 *
 *
 * "float" or "double"
 *
 *
 *
 *
 * "string"
 *
 *
 *
 *
 * "array"
 *
 *
 *
 *
 * "object"
 *
 *
 *
 *
 * "null"
 *
 *
 *
 * @throws VarException
 *
 */
function settype(&$var, string $type): void
{
    error_clear_last();
    $safeResult = \settype($var, $type);
    if ($safeResult === false) {
        throw VarException::createFromPhpError();
    }
=======
if (str_starts_with(PHP_VERSION, "8.1.") || str_starts_with(PHP_VERSION, "8.0.")) {
    require_once __DIR__ . '/8.1/var.php';
}
if (str_starts_with(PHP_VERSION, "8.2.")) {
    require_once __DIR__ . '/8.1/var.php';
}
if (str_starts_with(PHP_VERSION, "8.3.")) {
    require_once __DIR__ . '/8.1/var.php';
}
if (str_starts_with(PHP_VERSION, "8.4.")) {
    require_once __DIR__ . '/8.1/var.php';
}
if (str_starts_with(PHP_VERSION, "8.5.")) {
    require_once __DIR__ . '/8.1/var.php';
}
if (str_starts_with(PHP_VERSION, "8.6.")) {
    require_once __DIR__ . '/8.1/var.php';
>>>>>>> Stashed changes
}

