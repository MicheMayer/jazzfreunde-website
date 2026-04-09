<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Type\Primitive;

use InvalidArgumentException;
use Override;

/**
 * URI Typ
 */
final class Uri implements PrimitiveTypeInterface
{
    /**
     * @var non-empty-string
     */
    private readonly string $value;

    /**
     * @inheritDoc
     */
    public static function from(mixed $value): static
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('Value must be a string.');
        }

        return new self($value);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function tryFrom(mixed $value): static|null
    {
        if (!is_string($value)) {
            return null;
        }
        
        try {
            return new self($value);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('URI cannot be empty.');
        }

        $parts = parse_url($value);

        if(!is_array($parts)) {
            throw new InvalidArgumentException("'{$value}' is not a valid URI.");
        }


        if (!str_starts_with($value, '/')) {
            throw new InvalidArgumentException('URI must start with a leading slash.');
        }

        if (str_contains($value, ' ')) {
            throw new InvalidArgumentException('URI cannot contain spaces.');
        }

        if (isset($parts['scheme'])
            && isset($parts['host'])
            && isset($parts['user'])
            && isset($parts['pass'])
            && isset($parts['port'])
            && !isset($parts['path'])
            && !str_starts_with($parts['path'], '/')
        ) {
            throw new InvalidArgumentException("'{$value}' is not a valid URI.");
        }

        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->value();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function value(): string
    {
        return $this->value;
    }
}
