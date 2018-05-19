<?php

namespace App\Event;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Doctrine\ORM\EntityManager;
use App\Entity\User;

/**
 * Listener that updates the last activity of the authenticated user
 */
class ActivityListener
{
    protected $securityContext;
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->securityContext = $security;
        $this->entityManager = $entityManager;
    }

    /**
     * Update the user "lastActivity" on each request
     * @param FilterControllerEvent $event
     */
    public function onCoreController(FilterControllerEvent $event)
    {
        // Check that the current request is a "MASTER_REQUEST"
        // Ignore any sub-request
        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST) {
            return;
        }

        // Check token authentication availability
        if ($this->securityContext->getToken()) {
            $user = $this->securityContext->getToken()->getUser();

            if ( ($user instanceof User) && !($user->isActiveNow()) ) {
                $user->setLastActivity(new \DateTime());
                $this->entityManager->flush($user);
            }
        }
    }
}