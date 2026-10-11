<?php

session_start();

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Core\\'       => __DIR__ . '/core/',
        'Controllers\\' => __DIR__ . '/controllers/',
        'Models\\'     => __DIR__ . '/models/',
        'Config\\'     => __DIR__ . '/config/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (strpos($class, $prefix) !== 0) {
            continue;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
        return;
    }
});

use Core\App;

$app = new App();
$app->run();