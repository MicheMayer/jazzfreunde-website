<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Entity;

use ApiPlatform\Metadata as API;
use Doctrine\ORM\Mapping as ORM;
use Jazzfreunde\App\DependencyInjection\PropertyInjectionTrait;

/**
 * Event locations
 * @psalm-api
 */
#[ORM\Entity]
#[ORM\Table(name: 'event_locations')]
#[API\ApiResource(
    operations: [
        new API\Get(),
    ]
)]
class EventLocation
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
     * Name of the location
     *
     * @var string
     */
    #[ORM\Column(type: 'string')]
    #[API\ApiProperty(required: true)]
    public string $name;
}
