<?php

declare(strict_types=1);

namespace Jazzfreunde\App\Service\Notification\Provider\Source;

use Jazzfreunde\App\Api\Notification\Resource\EventNotification;
use Jazzfreunde\App\Exception\Notification\NotificationSourceNotFound;
use Jazzfreunde\App\Service\Event\EventRepositoryInterface;
use Jazzfreunde\App\Service\Notification\Provider\Model\NotificationResource;
use Jazzfreunde\App\Service\Notification\Provider\NotificationSourceInterface;
use Jazzfreunde\App\Service\Notification\Scope\ParsedScope;
use Jazzfreunde\App\Service\Twig\EmailTemplatePath;
use LogicException;
use Override;

/**
 * Notification source for event notifications.
 *
 * @psalm-api
 */
final class EventNotificationSource implements NotificationSourceInterface
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository
    ) {}

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function get(ParsedScope $parsedScope): NotificationResource
    {
        $entity = $parsedScope->dto;

        if (!$entity instanceof EventNotification) {
            throw new LogicException(sprintf('Expected an instance of %s.', EventNotification::class));
        }

        $event = $this->eventRepository->getById($entity->id);
        if (null === $event) {
            throw new NotificationSourceNotFound(sprintf('Event with ID %d not found.', $entity->id));
        }

        $eventMetadata = [
            'title' => $event->title,
            'subtitle' => $event->subtitle,
            'link' => $event->link,
            'location' => $event->location->name,
            'start' => $event->start,
        ];

        if ($event->start != $event->end) {
            $eventMetadata['end'] = $event->end;
        }

        return new NotificationResource(
            uri: $parsedScope->matchedScope,
            email_template: EmailTemplatePath::EVENT_NOTIFICATION,
            title: sprintf('Erinnerung: %s', $event->title),
            metadata: [
                'event' => $eventMetadata,
            ],
        );
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function canHandle(ParsedScope $parsedScope): bool
    {
        return $parsedScope->dto instanceof EventNotification;
    }
}
