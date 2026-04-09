<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Api\Notification;

use ApiPlatform\Metadata\ErrorResource;
use ApiPlatform\Metadata\Exception\ProblemExceptionInterface;
use Exception;
use Jazzfreunde\App\Service\Api\Provider\ErrorResourceSampleProvider;

#[ErrorResource(status: 409, provider: ErrorResourceSampleProvider::class)]
class RetrySubscribingError extends Exception implements ProblemExceptionInterface
{
    public function __construct(private string $baseUrl = '')
    {
    }

    public function getType(): string
    {
        return $this->baseUrl . '/problems/retry-subscribing';
    }

    public function getTitle(): ?string
    {
        return 'Retry subscribing';
    }

    public function getStatus(): ?int
    {
        return 409;
    }

    public function getDetail(): ?string
    {
        return 'A subscription with this email is already in progress for this resource scope.';
    }

    public function getInstance(): ?string
    {
        return null;
    }
}