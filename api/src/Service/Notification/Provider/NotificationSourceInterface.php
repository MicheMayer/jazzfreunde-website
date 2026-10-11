<?php

declare(strict_types=1);

namespace Jazzfreunde\App\Service\Notification\Provider;

use Jazzfreunde\App\Service\Notification\Provider\Model\NotificationResource;
use Jazzfreunde\App\Service\Notification\Scope\ParsedScope;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Interface for notification sources that provide notification resources for a specific notification type.
 */
#[AutoconfigureTag(NotificationSourceInterface::class)]
interface NotificationSourceInterface
{
    /**
     * Retrieves the notification resource for the given resource scope.
     *
     * @param ParsedScope $parsedScope
     *
     * @return NotificationResource
     */
    public function get(ParsedScope $parsedScope): NotificationResource;

    /**
     * Determines if the notification source can handle the given parsed scope.
     *
     * @param ParsedScope $parsedScope
     *
     * @return bool
     */
    public function canHandle(ParsedScope $parsedScope): bool;
}
