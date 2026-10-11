<?php

declare(strict_types=1);

namespace Jazzfreunde\App\Controller\Notification;

use Jazzfreunde\App\Exception\Contract\ConfirmationContractNotFoundException;
use Jazzfreunde\App\Service\Contract\ConfirmationContractService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles notification unsubscribe links from email.
 *
 * @psalm-api
 */
final class NotificationUnsubscribeController extends AbstractController
{
    public function __construct(
        private readonly ConfirmationContractService $confirmationContracts,
    ) {}

    /**
     * Cancels the notification subscription associated with the token.
     */
    #[Route('/notifications/unsubscribe/{token}', name: 'notification_unsubscribe', methods: ['GET'])]
    public function __invoke(string $token): Response
    {
        try {
            $this->confirmationContracts->cancelContract($token);
        } catch (ConfirmationContractNotFoundException) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        return $this->render('@pages/notifications/unsubscribed.html.twig');
    }
}
