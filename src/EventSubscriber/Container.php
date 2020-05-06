<?php
/**
 * @package App
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class Container
 * @package App\EventSubscriber
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */
class Container implements ServiceSubscriberInterface
{
    protected $locator;

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    public static function getSubscribedServices()
    {
        return [
            'doctrine.orm.entity_manager'    => EntityManagerInterface::class,
            'security.token_storage'         => TokenStorageInterface::class,
            'request_stack'                  => RequestStack::class,
            'security.authorization_checker' => AuthorizationCheckerInterface::class,
            'translator'                     => TranslatorInterface::class
        ];
    }

    protected function getUser()
    {
        $securityToken = $this->locator->get('security.token_storage');

        if (!$token = $securityToken->getToken()) {
            return null;
        }

        $user = $token->getUser();

        if (!($user instanceof UserInterface)) {
            return null;
        }

        return $user;
    }

    /**
     * Checks if the attributes are granted against the current authentication token and optionally supplied subject.
     *
     * @throws \LogicException
     */
    protected function isGranted($attributes, $subject = null): bool
    {
        return $this->locator->get('security.authorization_checker')->isGranted($attributes, $subject);
    }

}
