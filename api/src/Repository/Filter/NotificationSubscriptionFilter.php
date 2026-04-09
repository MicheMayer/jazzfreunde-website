<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Repository\Filter;

/**
 * Filter for NotificationSubscription entities.
 */
final class NotificationSubscriptionFilter
{
    public function __construct(
        /**
         * The resource scope for which the subscription is made.
         *
         * @var string
         */
        public string $resourceScope,

        /**
         * The email of the subscriber.
         *
         * @var string|null
         */
        public ?string $email = null,
    ) {
    }
}