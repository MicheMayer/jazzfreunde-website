<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Api\Notification;

use ApiPlatform\Metadata\ErrorResource;
use ApiPlatform\Metadata\Exception\ProblemExceptionInterface;
use Exception;
use Jazzfreunde\App\Service\Api\Provider\ErrorResourceSampleProvider;

#[ErrorResource(status: 422, provider: ErrorResourceSampleProvider::class)]
class AlreadySubscribedError extends Exception implements ProblemExceptionInterface
{
    public function __construct(private string $baseUrl = '')
    {
    }

    public function getType(): string
    {
        return $this->baseUrl . '/problems/already-subscribed';
    }

    public function getTitle(): ?string
    {
        return 'Already subscribed';
    }

    public function getStatus(): ?int
    {
        return 422;
    }

    public function getDetail(): ?string
    {
        return 'User with this email is already subscribed to this resource scope.';
    }

    public function getInstance(): ?string
    {
        return null;
    }
}