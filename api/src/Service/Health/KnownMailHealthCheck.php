<?php

declare(strict_types=1);

namespace Jazzfreunde\App\Service\Health;

use Doctrine\Persistence\ManagerRegistry;
use Jazzfreunde\App\Entity\KnownMail;
use Jazzfreunde\App\Type\Enum\HealthStatusEnum;
use Jazzfreunde\App\Type\Enum\KnownMailHandleEnum;
use Override;

/**
 * Checks that every known mail handle has a configured database entry.
 *
 * @psalm-api
 */
final class KnownMailHealthCheck implements HealthCheckInterface
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
    ) {}

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function checkHealth(): HealthStatusEnum
    {
        $knownMails = $this->doctrine->getRepository(KnownMail::class)->findAll();
        $configuredHandles = array_map(
            static fn (KnownMail $knownMail): KnownMailHandleEnum => $knownMail->handle,
            $knownMails,
        );

        foreach (KnownMailHandleEnum::cases() as $handle) {
            if (!in_array($handle, $configuredHandles, true)) {
                return HealthStatusEnum::Degraded;
            }
        }

        return HealthStatusEnum::Healthy;
    }
}
