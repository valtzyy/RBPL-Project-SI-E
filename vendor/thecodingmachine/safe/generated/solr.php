<?php

<<<<<<< Updated upstream
namespace Safe;

use Safe\Exceptions\SolrException;

/**
 * This function returns the current version of the extension as a string.
 *
 * @return string It returns a string on success.
 * @throws SolrException
 *
 */
function solr_get_version(): string
{
    error_clear_last();
    $safeResult = \solr_get_version();
    if ($safeResult === false) {
        throw SolrException::createFromPhpError();
    }
    return $safeResult;
=======
if (str_starts_with(PHP_VERSION, "8.1.") || str_starts_with(PHP_VERSION, "8.0.")) {
    require_once __DIR__ . '/8.1/solr.php';
}
if (str_starts_with(PHP_VERSION, "8.2.")) {
    require_once __DIR__ . '/8.1/solr.php';
}
if (str_starts_with(PHP_VERSION, "8.3.")) {
    require_once __DIR__ . '/8.1/solr.php';
}
if (str_starts_with(PHP_VERSION, "8.4.")) {
    require_once __DIR__ . '/8.1/solr.php';
}
if (str_starts_with(PHP_VERSION, "8.5.")) {
    require_once __DIR__ . '/8.1/solr.php';
}
if (str_starts_with(PHP_VERSION, "8.6.")) {
    require_once __DIR__ . '/8.1/solr.php';
>>>>>>> Stashed changes
}

