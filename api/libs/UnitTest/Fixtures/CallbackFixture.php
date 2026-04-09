<?php declare(strict_types = 1);

namespace Jazzfreunde\UnitTest\Fixtures;

use Closure;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * This ficture allows a custom setup of the database by providing a callback function.
 * The callback will be executed during the fixture loading process, allowing for dynamic data generation or complex setup logic that cannot be easily achieved with static fixtures.
 */
final class CallbackFixture implements FixtureInterface
{
    /**
     * Initialize fixture.
     */
    public function __construct(private Closure $callback)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        ($this->callback)($manager);
    }
}