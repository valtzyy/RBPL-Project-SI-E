<?php

<<<<<<< Updated upstream
namespace Safe;

use Safe\Exceptions\ClassobjException;

/**
 * Creates an alias named alias
 * based on the user defined class class.
 * The aliased class is exactly the same as the original class.
 *
 * @param string $class The original class.
 * @param string $alias The alias name for the class.
 * @param bool $autoload Whether to autoload if the original class is not found.
 * @throws ClassobjException
 *
 */
function class_alias(string $class, string $alias, bool $autoload = true): void
{
    error_clear_last();
    $safeResult = \class_alias($class, $alias, $autoload);
    if ($safeResult === false) {
        throw ClassobjException::createFromPhpError();
    }
=======
if (str_starts_with(PHP_VERSION, "8.1.") || str_starts_with(PHP_VERSION, "8.0.")) {
    require_once __DIR__ . '/8.1/classobj.php';
}
if (str_starts_with(PHP_VERSION, "8.2.")) {
    require_once __DIR__ . '/8.1/classobj.php';
}
if (str_starts_with(PHP_VERSION, "8.3.")) {
    require_once __DIR__ . '/8.1/classobj.php';
}
if (str_starts_with(PHP_VERSION, "8.4.")) {
    require_once __DIR__ . '/8.1/classobj.php';
}
if (str_starts_with(PHP_VERSION, "8.5.")) {
    require_once __DIR__ . '/8.1/classobj.php';
}
if (str_starts_with(PHP_VERSION, "8.6.")) {
    require_once __DIR__ . '/8.1/classobj.php';
>>>>>>> Stashed changes
}

