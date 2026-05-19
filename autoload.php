<?php

declare(strict_types=1);

/**
 * Simple autoloader — replaces Composer entirely.
 * Maps the AccountaBuddy\ namespace to the src/ directory.
 * e.g. AccountaBuddy\Discord\Api  →  src/Discord/Api.php
 */
spl_autoload_register(function (string $class): void {
    $prefix = 'AccountaBuddy\\';
    $base   = __DIR__ . '/src/';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file     = $base . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
