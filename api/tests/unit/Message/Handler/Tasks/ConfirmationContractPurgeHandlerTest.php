<?php declare(strict_types = 1);

namespace JazzfreundeTests\App\Tests\Message\Handler\Tasks;

use Jazzfreunde\App\Message\Handler\Tasks\ConfirmationContractPurgeHandler;
use Jazzfreunde\App\Service\Contract\ConfirmationContractPurgingInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the confirmation contract purge message handler.
 */
final class ConfirmationContractPurgeHandlerTest extends TestCase
{
    /**
     * Test dispatching the purge command to the repository.
     */
    public function testHandlePurgeVacantConfirmationContracts(): void
    {
        $repository = $this->createMock(ConfirmationContractPurgingInterface::class);
        $repository
            ->expects($this->once())
            ->method('purgeVacantContracts');

        $handler = new ConfirmationContractPurgeHandler($repository);
        $handler->handlePurgeVacantConfirmationContracts();
    }
}
