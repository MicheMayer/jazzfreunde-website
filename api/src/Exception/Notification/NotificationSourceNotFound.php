<?php

declare(strict_types=1);

namespace Jazzfreunde\App\Exception\Notification;

use RuntimeException;

/**
 * Exception thrown when a notification source is not found.
 */
final class NotificationSourceNotFound extends RuntimeException {}
