<?php

namespace App\Frontal\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Frontal\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class RedirectUserListener
 * @package App\Frontal\EventListener
 */
class RedirectUserListener
{
    /** @var TokenStorageInterface  */
    private $tokenStorage;

    /** @var RouterInterface  */
    private $router;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param RouterInterface $router
     */
    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->isMasterRequest() && $this->isUserLogged()) {
            $currentRoute = $event->getRequest()->attributes->get('_route');

            if ($this->isAuthenticatedUserOnAnonymousPage($currentRoute)) {
                $response = new RedirectResponse($this->router->generate('frontal.index'));
                $event->setResponse($response);
            }
        }
    }

    /**
     * @return bool
     */
    private function isUserLogged(): bool
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        return $token->getUser() instanceof User;
    }

    /**
     * @param string $currentRoute
     * @return bool
     */
    private function isAuthenticatedUserOnAnonymousPage(string $currentRoute): bool
    {
        return \in_array($currentRoute, ['frontal.login']);
    }
}
