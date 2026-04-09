<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jazzfreunde\App\Entity\Contract\ConfirmableEntity;

/**
 * Subscription for the newsletter
 * @psalm-api
 */
#[ORM\Entity]
#[ORM\Table(name: 'newsletter_subscriptions')]
class NewsletterSubscription extends ConfirmableEntity
{
}
