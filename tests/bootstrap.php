<?php

\error_reporting(E_ALL);

include_once \dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__ . '/../');
$dotenv->load();

if (! \function_exists('config')) {
    /**
     * Simple config() replacement for the few SWRETAIL_* envvars.
     *
     * @param string $name Example: "swretail.username"
     *
     * @return string The value of the config/env.
     */
    function config($name)
    {
        $key = \strtoupper(\str_replace('.', '_', $name));

        return \getenv($key);
    }
}
