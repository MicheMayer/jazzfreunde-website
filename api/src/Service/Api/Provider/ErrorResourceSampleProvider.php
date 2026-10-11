<?php

declare(strict_types=1);

namespace Jazzfreunde\App\Service\Api\Provider;

use ApiPlatform\Metadata\Exception\ProblemExceptionInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use InvalidArgumentException;
use Override;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides sample payloads for notification ErrorResource endpoints.
 *
 * @implements ProviderInterface<object>
 *
 * @psalm-api
 */
final class ErrorResourceSampleProvider implements ProviderInterface
{
    /**
     * {@inheritDoc}
     *
     * @psalm-suppress InvalidReturnStatement ApiPlatform permits array payloads for error resources.
     */
    #[Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null
    {
        $request = $context['request'] ?? null;
        $resourceClass = $context['resource_class'] ?? null;

        if (!$request instanceof Request) {
            throw new InvalidArgumentException(sprintf('Request object is required in context for %s.', self::class));
        }

        if (!is_string($resourceClass) || !is_subclass_of($resourceClass, ProblemExceptionInterface::class)) {
            return null;
        }

        $baseUrl = $request->getSchemeAndHttpHost();

        $error = new $resourceClass($baseUrl);

        return [
            'type' => $error->getType(),
            'title' => $error->getTitle(),
            'status' => $error->getStatus(),
            'detail' => $error->getDetail(),
            'instance' => $error->getInstance(),
        ];
    }
}
