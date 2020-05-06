<?php
/**
 * @package App
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

namespace App\Security\Voter;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * Class AbstractEntityVoter
 * @package App\Security\Voter
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */
abstract class AbstractEntityVoter extends Voter implements ServiceSubscriberInterface
{

    /**
     *
     */
    const LIST = 'list';

    /**
     *
     */
    const SHOW = 'show';

    /**
     *
     */
    const CREATE = 'new';

    /**
     *
     */
    const EDIT = 'edit';

    /**
     *
     */
    const DELETE = 'delete';

    /**
     *
     */
    const FILTERS = 'filters';

    /**
     *
     */
    const SEARCH = 'search';

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $locator;

    /**
     * Container constructor.
     *
     * @param \Psr\Container\ContainerInterface $locator
     */
    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @return array|string[]
     */
    public static function getSubscribedServices()
    {
        return [
            'security'                    => Security::class,
            'request_stack'               => RequestStack::class,
            'doctrine.orm.entity_manager' => EntityManagerInterface::class,
        ];
    }

    /**
     * @param string                                                               $attribute
     * @param mixed                                                                $subject
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($subject, $user, $attribute);
            case self::EDIT:
                return $this->canEdit($subject, $user, $attribute);
            case self::DELETE:
                return $this->canDelete($subject, $user, $attribute);
            case self::LIST:
                return $this->canList($subject, $user, $attribute);
            case self::SHOW:
                return $this->canShow($subject, $user, $attribute);
            case self::FILTERS:
                return $this->canFilters($subject, $user, $attribute);
            case self::SEARCH:
                return $this->canSearch($subject, $user, $attribute);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param                                                     $subject
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    abstract protected function canCreate($subject, UserInterface $user, $attribute);

    /**
     * @param                                                     $subject
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    abstract protected function canEdit($subject, UserInterface $user, $attribute);

    /**
     * @param                                                     $subject
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    abstract protected function canDelete($subject, UserInterface $user, $attribute);

    /**
     * @param                                                     $subject
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    abstract protected function canList($subject, UserInterface $user, $attribute);

    /**
     * @param                                                     $subject
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    abstract protected function canShow($subject, UserInterface $user, $attribute);

    /**
     * @param                                                     $subject
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    abstract protected function canFilters($subject, UserInterface $user, $attribute);

    /**
     * @param                                                     $subject
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    abstract protected function canSearch($subject, UserInterface $user, $attribute);

}