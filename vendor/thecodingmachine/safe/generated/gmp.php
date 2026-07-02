<?php

<<<<<<< Updated upstream
namespace Safe;

use Safe\Exceptions\GmpException;

/**
 *
 *
 * @param \GMP|string|int $seed The seed to be set for the gmp_random,
 * gmp_random_bits, and
 * gmp_random_range functions.
 *
 * A GMP object, an integer or a numeric string.
 * @throws GmpException
 *
 */
function gmp_random_seed($seed): void
{
    error_clear_last();
    $safeResult = \gmp_random_seed($seed);
    if ($safeResult === false) {
        throw GmpException::createFromPhpError();
    }
=======
if (str_starts_with(PHP_VERSION, "8.1.") || str_starts_with(PHP_VERSION, "8.0.")) {
    require_once __DIR__ . '/8.1/gmp.php';
}
if (str_starts_with(PHP_VERSION, "8.2.")) {
    require_once __DIR__ . '/8.2/gmp.php';
}
if (str_starts_with(PHP_VERSION, "8.3.")) {
    require_once __DIR__ . '/8.2/gmp.php';
}
if (str_starts_with(PHP_VERSION, "8.4.")) {
    require_once __DIR__ . '/8.2/gmp.php';
}
if (str_starts_with(PHP_VERSION, "8.5.")) {
    require_once __DIR__ . '/8.2/gmp.php';
}
if (str_starts_with(PHP_VERSION, "8.6.")) {
    require_once __DIR__ . '/8.2/gmp.php';
>>>>>>> Stashed changes
}

