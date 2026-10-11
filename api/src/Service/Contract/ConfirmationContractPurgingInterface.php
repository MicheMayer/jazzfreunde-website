<?php

declare(strict_types=1);

namespace Jazzfreunde\App\Service\Contract;

/**
 * Purges expired and unconfirmed confirmation contracts.
 */
interface ConfirmationContractPurgingInterface
{
    public function purgeVacantContracts(): void;
}
