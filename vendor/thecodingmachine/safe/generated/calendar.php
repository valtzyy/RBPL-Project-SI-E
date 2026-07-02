<?php

<<<<<<< Updated upstream
namespace Safe;

use Safe\Exceptions\CalendarException;

/**
 * Return the Julian Day for a Unix timestamp
 * (seconds since 1.1.1970), or for the current day if no
 * timestamp is given. Either way, the time is regarded
 * as local time (not UTC).
 *
 * @param int $timestamp A unix timestamp to convert.
 * @return int A julian day number as integer.
 * @throws CalendarException
 *
 */
function unixtojd(int $timestamp = null): int
{
    error_clear_last();
    if ($timestamp !== null) {
        $safeResult = \unixtojd($timestamp);
    } else {
        $safeResult = \unixtojd();
    }
    if ($safeResult === false) {
        throw CalendarException::createFromPhpError();
    }
    return $safeResult;
=======
if (str_starts_with(PHP_VERSION, "8.1.") || str_starts_with(PHP_VERSION, "8.0.")) {
    require_once __DIR__ . '/8.1/calendar.php';
}
if (str_starts_with(PHP_VERSION, "8.2.")) {
    require_once __DIR__ . '/8.1/calendar.php';
}
if (str_starts_with(PHP_VERSION, "8.3.")) {
    require_once __DIR__ . '/8.1/calendar.php';
}
if (str_starts_with(PHP_VERSION, "8.4.")) {
    require_once __DIR__ . '/8.1/calendar.php';
}
if (str_starts_with(PHP_VERSION, "8.5.")) {
    require_once __DIR__ . '/8.1/calendar.php';
}
if (str_starts_with(PHP_VERSION, "8.6.")) {
    require_once __DIR__ . '/8.1/calendar.php';
>>>>>>> Stashed changes
}

