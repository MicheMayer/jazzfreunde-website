<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Event\Listener\Api\Contract;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use Jazzfreunde\App\Api\Notification\AlreadySubscribedError;
use Jazzfreunde\App\Api\Notification\NotificationSubscription;
use Jazzfreunde\App\Api\Notification\RetrySubscribingError;
use Jazzfreunde\App\Entity\Contract\ConfirmableEntity;
use Jazzfreunde\App\Repository\Filter\NotificationSubscriptionFilter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Jazzfreunde\App\Service\Contract\ConfirmationContractService;
use Jazzfreunde\App\Service\Notification\NotificationSubscriptionRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Starts the confirmation process for entities that implement ConfirmableInterface.
 * @psalm-api
 */
#[AsEventListener(event: KernelEvents::VIEW, method: 'validateExistingSubscription', priority: EventPriorities::POST_VALIDATE)]
#[AsEventListener(event: KernelEvents::VIEW, method: 'startConfirmationProcess', priority: EventPriorities::PRE_WRITE)]
final class WriteConfirmationListener
{
    /**
    * @param ConfirmationContractService $confirmationContracts
    * @psalm-suppress PossiblyUnusedMethod
    */
    public function __construct(
        private ConfirmationContractService $confirmationContracts,
        private NotificationSubscriptionRepositoryInterface $notificationSubscriptionRepository
    ) {
    }

    public function validateExistingSubscription(ViewEvent $event): void
    {
        $request = $event->getRequest();

        if (Request::METHOD_POST !== $request->getMethod()) {
            return;
        }

        $controllerResult = $event->getControllerResult();
        
        if (!$controllerResult instanceof NotificationSubscription) {
            return;
        }

        $existingSubscription = $this->notificationSubscriptionRepository->findOneByFilter(
            new NotificationSubscriptionFilter(
                resourceScope: $controllerResult->resourceScope,
                email: $controllerResult->email
            )
        );

        if ($existingSubscription === null) {
            return;
        }

        if ($existingSubscription->confirmation->isConfirmed()) {
            throw new AlreadySubscribedError($request->getSchemeAndHttpHost());
        }

        $this->confirmationContracts->restartEmailConfirmation(
            $existingSubscription->confirmation,
            $existingSubscription->email
        );

        throw new RetrySubscribingError($request->getSchemeAndHttpHost());
    }

    /**
     * Starts the confirmation process for entities that implement ConfirmableInterface after they have been written to the database.
     * @psalm-api
     */
    public function startConfirmationProcess(ViewEvent $event): void
    {
        $method = $event->getRequest()->getMethod();

        if (Request::METHOD_POST !== $method) {
            return;
        }

        $controllerResult = $event->getControllerResult();

        if (!$controllerResult instanceof ConfirmableEntity) {
            return;
        }
        
        $confirmationContract = $controllerResult->confirmation;
        $email = $controllerResult->email;

        $this->confirmationContracts->startEmailConfirmation(
            $confirmationContract,
            $email
        );
    }
}
