<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Jazzfreunde\App\DependencyInjection\PropertyInjectionTrait;
use Jazzfreunde\App\Entity\Contract\ConfirmableEntity;
use Jazzfreunde\App\Entity\Type\String\UriType;
use Jazzfreunde\App\Type\Primitive\Uri;

/**
 * Subscription for any type of notifications
 * @psalm-api
 */
#[ORM\Entity]
#[ORM\Table(name: 'notification_subscriptions')]
#[UniqueEntity(
    fields: ['resourceScope', 'email'],
    message: 'This email is already subscribed to this resource scope.',
    errorPath: 'email',
)]
class NotificationSubscription extends ConfirmableEntity
{
    use PropertyInjectionTrait;

    /**
     * URI associated with the subscription (e.g., endpoint for push notifications)
     *
     * @var Uri
     */
    #[ORM\Column(type: UriType::ENTITY_NAME, unique: true)]
    public Uri $resourceScope;

    /**
     * Create a new contract
     * @psalm-suppress UndefinedThisPropertyFetch
     */
    public function __construct()
    {
        parent::__construct();
    }
}
