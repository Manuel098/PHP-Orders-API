<?php

/**
 * Load all namespaces on App
 */
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));

    $file = __DIR__ . '/App/' . str_replace( '\\', '/', $relativeClass ) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});