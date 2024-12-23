<?php

declare(strict_types=1);

namespace App\Framework\JsonSchemaInput;

use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class JsonSchemaInputValueResolver implements ValueResolverInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    /** @return iterable<JsonSchemaInput> */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!\is_subclass_of($argument->getType(), JsonSchemaInput::class)) {
            return [];
        }

        try {
            $json = \json_decode((string) $request->getContent(), flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $json = new \stdClass();
        }

        /** @var class-string<JsonSchemaInput> $schema */
        $schema = $argument->getType();

        $result = (new Validator())->validate($json, \json_encode($schema::getSchema()));

        if (!$result->isValid()) {
            $error = $result->error();
            $this->logger->error('JSON schema validation error', ['error' => $error]);

            throw new BadRequestHttpException(\json_encode((new ErrorFormatter())->formatOutput($error, 'basic')));
        }

        yield from [$schema::fromPayload(\json_decode(\json_encode($json), associative: true))];
    }
}
