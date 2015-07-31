<?php

error_reporting(E_ALL);

defined('PHALBEE_START') or define('PHALBEE_START', microtime(true));

defined('ROOT_DIR') or define('ROOT_DIR', __DIR__ . '/..');
defined('APP_PATH') or define('APP_PATH', realpath('..') . '/');

// comment out the following two lines when deployed to production
defined('APP_DEBUG') or define('APP_DEBUG', true);
defined('APP_ENV') or define('APP_ENV', 'dev');

require ROOT_DIR . '/bootstrap/autoload.php';
require ROOT_DIR . '/bootstrap/app.php';

try {
    $application = new Application();
    $application->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
