<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Validation\Attribute;

use Attribute;
use Jazzfreunde\App\Validation\Validator\UriValidator;
use Override;
use Symfony\Component\Validator\Constraint;

/**
 * Attribute to validate a URI path to a resource.
 *
 * The URI must be relative and start with a leading slash.
 *
 * @psalm-api
 */
#[Attribute]
final class Uri extends Constraint
{
    public string $message = 'The string "{{ string }}" is not a valid relative URI path with a leading slash.';

    /**
     * @param string|null $message
     * @param array<string, string>|null $groups
     * @param mixed|null $payload
     */
    public function __construct(?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct(null, $groups, $payload);

        $this->message = $message ?? $this->message;
    }

    /**
     * Returns the name of the validator class.
     *
     * @return string
     */
    #[Override]
    public function validatedBy(): string
    {
        return UriValidator::class;
    }
}
