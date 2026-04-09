<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Service\Notification\Source;

use Doctrine\ORM\EntityManagerInterface;
use Jazzfreunde\App\Type\Primitive\Uri;

/**
 * Notification source for event-related notifications.
 * This source provides notifications related to events, such as event updates, cancellations, or reminders.
 */
final class EventNotificationSource implements NotificationSourceInterface
{
    /**
     * Constructor for EventNotificationSource.
     *
     * @param EntityManagerInterface $entityManager The entity manager for database interactions.
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getResourceUri(string $slug): Uri
    {
        return new Uri("/events/$slug");
    }
}
