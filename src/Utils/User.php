<?php
/**
 * @package App
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

namespace App\Utils;

use App\Entity\User as UserEntity;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class User
 * @package App\Utils
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */
class User implements ServiceSubscriberInterface
{
    private $locator;

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    public static function getSubscribedServices()
    {
        return [
            'security.user_password_encoder.generic' => UserPasswordEncoderInterface::class,
            'doctrine.orm.entity_manager'            => EntityManagerInterface::class,
        ];
    }


    public function createUser(FormInterface $form)
    {
        $passwordEncoder = $this->locator->get('security.user_password_encoder.generic');
        $entityManager   = $this->locator->get('doctrine.orm.entity_manager');
        $user            = $form->getData();

        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );
        $roles   = ['ROLE_USER'];
        $roles[] = 'ROLE_' . strtoupper(UserEntity::AVAILABLE_TYPES[$form->get('type')->getData()]);
        $roles[] = 'ROLE_' . strtoupper(UserEntity::AVAILABLE_TYPES[$form->get('type')->getData()]) . '_CREATE';
        $roles[] = 'ROLE_' . strtoupper(UserEntity::AVAILABLE_TYPES[$form->get('type')->getData()]) . '_EDIT';
        $roles[] = 'ROLE_' . strtoupper(UserEntity::AVAILABLE_TYPES[$form->get('type')->getData()]) . '_LIST';
        $roles[] = 'ROLE_' . strtoupper(UserEntity::AVAILABLE_TYPES[$form->get('type')->getData()]) . '_DELETE';
        $user->setRoles($roles);

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;

    }

    public function sendSuccessfulRegisterationMail(UserInterface $user)
    {
        //TODO: implement
    }

}
