<?php

declare(strict_types=1);

use App\Framework\Kernel;

require_once \dirname(__DIR__) . '/vendor/autoload_runtime.php';

if ($_ENV['HTTPS'] ?? false) {
    $_SERVER['HTTPS'] = $_ENV['HTTPS'];
}

if ($_ENV['HOST'] ?? false) {
    $_SERVER['HTTP_HOST'] = $_ENV['HOST'];
}

return static fn (array $context) => new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
