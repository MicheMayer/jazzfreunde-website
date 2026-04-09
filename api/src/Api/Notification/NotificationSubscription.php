<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Api\Notification;

use ApiPlatform\Metadata as API;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\OpenApi\Model\MediaType;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\OpenApi\Model\Response as OpenApiResponse;
use Jazzfreunde\App\Entity\Notification\NotificationSubscription as NotificationEntity;
use Jazzfreunde\App\Type\Primitive\Email;
use Jazzfreunde\App\Type\Primitive\Uri;
use Jazzfreunde\App\Validation\Attribute as CustomAssert;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\ObjectMapper\Condition\TargetClass;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Subscription for any type of notifications
 * @psalm-api
 */
#[API\ApiResource(
    shortName: 'NotificationSubscription',
    stateOptions: new Options(entityClass: NotificationEntity::class),
    operations: [
        new Api\Post(
            errors: [
                AlreadySubscribedError::class,
                RetrySubscribingError::class,
            ],
        ),
    ]
)]
#[Map(target: NotificationEntity::class)]
#[Map(source: NotificationEntity::class)]
final class NotificationSubscription
{
    /**
     * URI associated with the subscription (e.g., endpoint for push notifications)
     *
     * @var string
     */
    #[CustomAssert\Uri(message: 'The string "{{ string }}" is not a valid relative URI path with a leading slash.')]
    #[API\ApiProperty(openapiContext: ['example' => '/events/123'], required: true)]
    #[Map(if: new TargetClass(NotificationEntity::class), transform: [Uri::class, 'from'])]
    #[Map(if: new TargetClass(self::class), transform: [Uri::class, 'value'])]
    public string $resourceScope;

    /**
     * Email address associated with the subscription
     *
     * @var string
     */
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    #[API\ApiProperty(required: true)]
    #[Map(if: new TargetClass(NotificationEntity::class), transform: [Email::class, 'from'])]
    #[Map(if: new TargetClass(self::class), transform: [Email::class, 'value'])]
    public string $email;
}
