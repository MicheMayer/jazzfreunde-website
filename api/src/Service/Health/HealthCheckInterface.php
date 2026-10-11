<?php

declare(strict_types=1);

namespace Jazzfreunde\App\Service\Health;

use Jazzfreunde\App\Type\Enum\HealthStatusEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Provides a health status for one application dependency or subsystem.
 */
#[AutoconfigureTag(HealthCheckInterface::class)]
interface HealthCheckInterface
{
    /**
     * Checks the health of the implementing service.
     */
    public function checkHealth(): HealthStatusEnum;
}
