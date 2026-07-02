<?php

<<<<<<< Updated upstream
namespace Safe;

use Safe\Exceptions\UopzException;

/**
 * Makes class extend parent
 *
 * @param string $class The name of the class to extend
 * @param string $parent The name of the class to inherit
 * @throws UopzException
 *
 */
function uopz_extend(string $class, string $parent): void
{
    error_clear_last();
    $safeResult = \uopz_extend($class, $parent);
    if ($safeResult === false) {
        throw UopzException::createFromPhpError();
    }
=======
if (str_starts_with(PHP_VERSION, "8.1.") || str_starts_with(PHP_VERSION, "8.0.")) {
    require_once __DIR__ . '/8.1/uopz.php';
>>>>>>> Stashed changes
}


/**
 * Makes class implement interface
 *
 * @param string $class
 * @param string $interface
 * @throws UopzException
 *
 */
function uopz_implement(string $class, string $interface): void
{
    error_clear_last();
    $safeResult = \uopz_implement($class, $interface);
    if ($safeResult === false) {
        throw UopzException::createFromPhpError();
    }
}

