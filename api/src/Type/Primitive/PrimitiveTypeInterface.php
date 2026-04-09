<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Type\Primitive;

use Stringable;
use InvalidArgumentException;

/**
 * Interface for primitive types
 */
interface PrimitiveTypeInterface extends Stringable
{
    /**
     * Create a new instance from a value
     *
     * @param mixed $value
     * @return static
     * @throws InvalidArgumentException if the value is invalid
     */
    public static function from(mixed $value): static;

    /**
     * Create a new instance from a value
     *
     * @param mixed $value
     * @return static|null
     */
    public static function tryFrom(mixed $value): static|null;

    /**
     * Get the value as string
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Returns the value of the primitive type.
     *
     * @return non-empty-string
     */
    public function value(): string;
}
