<?php

declare(strict_types=1);

namespace App\Framework\EnvVarProcessor;

use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

final readonly class TrimTrailingSlashEnvVarProcessor implements EnvVarProcessorInterface
{
    public function getEnv(string $prefix, string $name, \Closure $getEnv): string
    {
        return \trim($getEnv($name), '/');
    }

    public static function getProvidedTypes(): array
    {
        return [
            'trim-trailing-slash' => 'string',
        ];
    }
}
