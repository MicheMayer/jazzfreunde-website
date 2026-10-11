<?php

declare(strict_types=1);

namespace Jazzfreunde\App\Entity\Type\Enum\Event;

use Jazzfreunde\App\Entity\Type\Enum\Abstract\AbstractEnumType;
use Jazzfreunde\App\Type\Enum\EventCategoryEnum;

/**
 * Veranstaltungskategorien.
 *
 * @extends AbstractEnumType<EventCategoryEnum>
 */
final class EventCategoryType extends AbstractEnumType
{
    /**
     * @override
     */
    public const string ENTITY_NAME = 'event_category';

    /**
     * {@inheritDoc}
     */
    public const string ENUM_CLASS_NAME = EventCategoryEnum::class;
}
