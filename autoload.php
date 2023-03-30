<?php
// autoload.php

spl_autoload_register(function ($class) {
    // Prefix for your namespace
    $prefix = 'EHUmfragen\\';

    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/classes/';

    // Check if the class uses the prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // If not, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace namespace separators with directory separators and add .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
