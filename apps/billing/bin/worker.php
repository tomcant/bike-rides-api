<?php

declare(strict_types=1);

use App\Framework\Kernel;
use Bref\Symfony\Messenger\Service\Sqs\SqsConsumer;

require \dirname(__DIR__) . '/vendor/autoload.php';

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) ($_SERVER['APP_DEBUG'] ?? false));
$kernel->boot();

return $kernel->getContainer()->get(SqsConsumer::class);
