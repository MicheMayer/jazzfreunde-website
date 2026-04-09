<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Jazzfreunde\App\DependencyInjection\PropertyInjectionTrait;
use Jazzfreunde\App\Entity\Type\Enum\Event\EventCategoryType;
use Jazzfreunde\App\Type\Enum\EventCategoryEnum;

/**
 * Entity representing a planned event (e.g., a concert, meeting, etc.)
 * @psalm-api
 */
#[ORM\Entity]
#[ORM\Table(name: 'events')]
class Event
{
    use PropertyInjectionTrait;

    /**
     * Unique identifier in the database
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    /**
     * Title of the event
     *
     * @var string
     */
    #[ORM\Column(type: 'string')]
    public string $title;

    /**
    * Start time of the event
    *
    * @var DateTime
    */
    #[ORM\Column(type: 'datetime')]
    public DateTime $start;

    /**
     * End time of the event
     *
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime')]
    public DateTime $end;

    /**
    * Location of the event
    *
    * @var EventLocation
    */
    #[ORM\ManyToOne(targetEntity: EventLocation::class)]
    #[ORM\JoinColumn(nullable: false)]
    public EventLocation $location;

    /**
     * Optional subtitle for the event
     *
     * @var string|null
     */
    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $subtitle = null;

    /**
     * Optional description for the event
     *
     * @var string|null
     */
    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $link = null;

    /**
    * Category of the event (e.g., concert, meeting, etc.)
    *
    * @var EventCategoryEnum
    */
    #[ORM\Column(type: EventCategoryType::ENTITY_NAME, options: [ 'default' => EventCategoryEnum::DEFAULT ])]
    public EventCategoryEnum $category = EventCategoryEnum::default;
}
