<?php
/**
 * @package App
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

namespace App\EventSubscriber;

use App\Entity\CitizenInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Description of DoctrineSubscriber
 *
 * @author 13
 */
class DoctrineSubscriber extends Container implements EventSubscriber
{
    /**
     * @return array|string[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist
        ];
    }

    /**
     * @param \Doctrine\Persistence\Event\LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        $this->setCitizen($entity);

    }

    protected function setCitizen($entity)
    {
        if ($entity instanceof CitizenInterface && $user = $this->getUser()) {
            $entity->setCitizen($user);
        }
    }

}
