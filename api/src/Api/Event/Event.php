<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Api\Event;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata as API;
use DateTime;
use Jazzfreunde\App\Entity\Event as EventEntity;
use Jazzfreunde\App\Entity\EventLocation;
use Jazzfreunde\App\Type\Enum\EventCategoryEnum;
use Symfony\Component\ObjectMapper\Attribute\Map;

/**
 * API resource for planned events (e.g., concerts, meetings, etc.)
 * @psalm-api
 */
#[API\ApiResource(
    shortName: 'Event',
    stateOptions: new Options(entityClass: EventEntity::class),
    operations: [
        new API\Get(),
        new API\GetCollection()
    ],
    paginationClientItemsPerPage: true
)]
#[API\ApiFilter(DateFilter::class, properties: ['start'])]
#[API\ApiFilter(OrderFilter::class, properties: ['start'])]
#[Map(source: EventEntity::class)]
final class Event
{
    /**
     * Unique identifier in the database
     *
     * @var int|null
     */
    public ?int $id = null;

    /**
     * Title of the event
     *
     * @var string
     */
    #[API\ApiProperty(required: true)]
    public string $title;

    /**
     * Start time of the event
     *
     * @var DateTime
     */
    #[API\ApiProperty(required: true)]
    public DateTime $start;

    /**
     * End time of the event
     *
     * @var DateTime
     */
    #[API\ApiProperty(required: true)]
    public DateTime $end;

    /**
     * Location of the event
     *
     * @var EventLocation
     */
    #[API\ApiProperty(readableLink: true, writableLink: false, required: true)]
    public EventLocation $location;

    /**
     * Optional subtitle for the event
     *
     * @var string|null
     */
    public ?string $subtitle = null;

    /**
     * Optional description for the event
     *
     * @var string|null
     */
    public ?string $link = null;

    /**
     * Category of the event (e.g., concert, meeting, etc.)
     *
     * @var EventCategoryEnum
     */
    #[API\ApiProperty(required: true)]
    public EventCategoryEnum $category = EventCategoryEnum::default;
}
