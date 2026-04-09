<?php declare(strict_types = 1);

namespace Jazzfreunde\UnitTest\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Jazzfreunde\App\Entity\KnownMail;
use Jazzfreunde\App\Type\Enum\KnownMailHandleEnum;
use Jazzfreunde\App\Type\Primitive\Email;

/**
 * Sets up {@see KnownMail}
 */
final class KnownEmailsFixture implements FixtureInterface
{
    private array $knownEmails;
    
    /**
     * Initialize fixture with default known emails.
     */
    public function __construct()
    {
        $this->knownEmails = [
            new KnownMail(handle: KnownMailHandleEnum::NoReply, address: new Email('no-reply@example.com')),
            new KnownMail(handle: KnownMailHandleEnum::Jazzletter, address: new Email('jazzletter@example.com'))
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        foreach ($this->knownEmails as $user) {
            $manager->persist($user);
        }
        $manager->flush();
    }
}