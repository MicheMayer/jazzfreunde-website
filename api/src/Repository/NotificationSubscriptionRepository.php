<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Jazzfreunde\App\Entity\Notification\NotificationSubscription;
use Jazzfreunde\App\Repository\Filter\NotificationSubscriptionFilter;
use Jazzfreunde\App\Service\Notification\NotificationSubscriptionRepositoryInterface;

/**
 * Repository for managing {@see NotificationSubscription} entities.
 * @psalm-api
 */
final class NotificationSubscriptionRepository extends ServiceEntityRepository implements NotificationSubscriptionRepositoryInterface
{
    /**
     * Init repository
     *
     * @param  ManagerRegistry $registry
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress UnusedParam
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationSubscription::class);
    }

    /**
    * {@inheritDoc}
    */
    public function findByFilter(NotificationSubscriptionFilter $filter): array
    {
        $qb = $this->createQueryBuilder('subscription')
            ->where('subscription.resourceScope LIKE :resourceScopePrefix')
            ->setParameter('resourceScopePrefix', $filter->resourceScope . '%');

        if ($filter->email !== null) {
            $qb
                ->andWhere('subscription.email = :email')
                ->setParameter('email', $filter->email);
        }

        /** @var array<NotificationSubscription> $subscriptions */
        $subscriptions = $qb->getQuery()->getResult();

        return $subscriptions;
    }

    /**
    * {@inheritDoc}
    */
    public function findOneByFilter(NotificationSubscriptionFilter $filter): ?NotificationSubscription
    {
        $subscriptions = $this->findByFilter($filter);
        return array_shift($subscriptions);
    }
}