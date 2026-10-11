<?php

declare(strict_types=1);

namespace Jazzfreunde\App\Service\Notification\Scope\Parameter;

use Override;

/**
 * Number parameter in a notification scope definition.
 */
final class Number extends AbstractParameter
{
    #[Override]
    public function getRegex(): string
    {
        return '\d+';
    }

    /** @psalm-api */
    public static function parse(string $value): int
    {
        return (int) $value;
    }
}
