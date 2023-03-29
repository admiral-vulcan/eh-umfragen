<?php
spl_autoload_register(function ($class_name) {
    if (str_contains($class_name, "\\")) $class_name = substr($class_name, strrpos($class_name, '\\') + 1);
    $file = __DIR__ . '/classes/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
?>