<?php
/**
 * @package App
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class RequestSubscriber
 * @package App\EventSubscriber
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */
class RequestSubscriber extends Container implements EventSubscriberInterface
{

    /**
     * @return array|\array[][]
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest']],
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if ( $user = $this->getUser()) {
            $em = $this->locator->get('doctrine.orm.entity_manager');
            $filter      = $em->getFilters()->enable('user_filter');
            $filterParam = $user ? $user->getId() : null;

            if ($this->isGranted('ROLE_CITIZEN')) {
                $filter->setParameter('citizen_id', $filterParam);
            }

            if ($this->isGranted('ROLE_LAWYER')) {
                $filter->setParameter('lawyer_id', $filterParam);
            }

        }
    }
}
