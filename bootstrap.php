<?php
/**
 * Project bootstrap.
 *
 * Sets up class autoloading and is required by every entry-point PHP file
 * before any application classes are used. This means the project runs
 * out-of-the-box without needing `composer install` — useful for the
 * web app itself. Composer is still required if you want to run the
 * test suite locally (it pulls in PHPUnit), but day-to-day development
 * does not need it.
 */

// Map App\Foo → src/Foo.php and Tests\Foo → tests/Foo.php.
spl_autoload_register(function (string $class): void {
    $prefixes = [
        'App\\'   => __DIR__ . '/src/',
        'Tests\\' => __DIR__ . '/tests/',
    ];
    foreach ($prefixes as $prefix => $baseDir) {
        if (str_starts_with($class, $prefix)) {
            $relative = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
            if (is_file($file)) {
                require $file;
            }
            return;
        }
    }
});
