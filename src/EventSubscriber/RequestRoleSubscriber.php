<?php
/**
 * @package App
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

namespace App\EventSubscriber;

use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use EasyCorp\Bundle\EasyAdminBundle\Exception\NoPermissionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Description of RequestAccessControlSubscriber
 *
 * @author 13
 */
class RequestRoleSubscriber extends Container implements EventSubscriberInterface
{
    /**
     * @return array|\string[][]
     */
    public static function getSubscribedEvents()
    {
        return [
            EasyAdminEvents::POST_INITIALIZE => ['onKernelRequest'],
        ];
    }

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     *
     */
    public function onKernelRequest(GenericEvent $event)
    {
        $entity  = $event->getArgument('entity');
        $request = $event->getArgument('request');
        $action  = $request->query->get('action') ?? 'list';

        if (!$this->isGranted($action, $entity['class'])) {
            throw new NoPermissionException([
                'entity_name' => $entity['class'],
                'action'      => $action,
                'entity_id'   => null
            ]);
        }
    }

}
