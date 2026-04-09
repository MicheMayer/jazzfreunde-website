<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Service\Notification;

use Jazzfreunde\App\Entity\Notification\NotificationSubscription;
use Jazzfreunde\App\Repository\Filter\NotificationSubscriptionFilter;

/**
 * Interface for the NotificationSubscription repository.
 */
interface NotificationSubscriptionRepositoryInterface
{
    /**
     * Finds subscriptions based on the provided filter criteria.
     *
     * @param NotificationSubscriptionFilter $filter The filter criteria for finding subscriptions.
     * @return NotificationSubscription[] An array of matching NotificationSubscription entities.
     */
    public function findByFilter(NotificationSubscriptionFilter $filter): array;

    /**
     * Finds a single subscription based on the provided filter criteria.
     *
     * @param NotificationSubscriptionFilter $filter The filter criteria for finding a subscription.
     * @return NotificationSubscription|null A matching NotificationSubscription entity or null if not found.
     */
    public function findOneByFilter(NotificationSubscriptionFilter $filter): ?NotificationSubscription;
}