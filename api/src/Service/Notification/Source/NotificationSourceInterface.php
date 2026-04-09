<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Service\Notification\Source;

use Jazzfreunde\App\Entity\NotificationSubscription;
use Jazzfreunde\App\Type\Primitive\Uri;

/**
 * Notification source decribes a set of notifications that are available for subscription.
 */
interface NotificationSourceInterface
{
    /**
     * Get the unique identifier of the notification source.
     *
     * @return Uri
     */
    public function getResourceUri(string $slug): Uri;
}
