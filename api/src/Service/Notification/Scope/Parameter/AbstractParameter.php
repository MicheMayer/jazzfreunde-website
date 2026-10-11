<?php

declare(strict_types=1);

namespace Jazzfreunde\App\Service\Notification\Scope\Parameter;

/**
 * Abstract base class for parameters in notification scope definitions.
 */
abstract class AbstractParameter
{
    public function __construct(
        protected string $name,
    ) {}

    /**
     * Gets the name of the parameter.
     *
     * @return string the name of the parameter
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the regex pattern for validating the parameter value.
     *
     * @return string the regex pattern for the parameter
     */
    abstract public function getRegex(): string;
}
