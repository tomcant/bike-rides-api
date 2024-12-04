<?php

declare(strict_types=1);

require \dirname(__DIR__) . '/vendor/autoload.php';

if (\file_exists(\dirname(__DIR__) . '/config/bootstrap.php')) {
    require \dirname(__DIR__) . '/config/bootstrap.php';
}

if ($_SERVER['APP_DEBUG']) {
    \umask(0);
}
