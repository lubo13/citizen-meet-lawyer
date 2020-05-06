<?php
/**
 * @package App
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

namespace App\Security\Voter;

use App\Entity\AppointmentDatetime;
use App\Entity\AppointmentDatetimeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AppointmentDatetimeVoter
 * @package App\Security\Voter
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */
class AppointmentDatetimeVoter extends AbstractEntityVoter
{
    const ALLOWED_FIELDS_XML = ['accepted'];

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::CREATE, self::EDIT, self::LIST, self::DELETE, self::SHOW])) {
            return false;
        }

        if ($subject instanceof AppointmentDatetimeInterface) {
            return true;
        } elseif ($subject === AppointmentDatetime::class) {
            return true;
        }elseif ($subject instanceof \ReflectionClass && $subject->implementsInterface(AppointmentDatetimeInterface::class)) {
            return true;
        }
        return false;
    }

    /**
     * @param                                                     $appointmentDatetime
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    protected function canCreate($appointmentDatetime, UserInterface $user, $attribute)
    {
        return $this->locator->get('security')->isGranted('ROLE_CITIZEN');
    }

    /**
     * @param                                                     $appointmentDatetime
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    protected function canList($appointmentDatetime, UserInterface $user, $attribute)
    {
        return true;
    }

    /**
     * @param                                                     $appointmentDatetime
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    protected function canShow($appointmentDatetime, UserInterface $user, $attribute)
    {
        return true;
    }

    /**
     * @param                                                     $appointmentDatetime
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    protected function canFilters($appointmentDatetime, UserInterface $user, $attribute)
    {
        return true;
    }

    /**
     * @param                                                     $appointmentDatetime
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    protected function canSearch($appointmentDatetime, UserInterface $user, $attribute)
    {
        return true;
    }

    /**
     * @param                                                     $appointmentDatetime
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return bool|mixed
     */
    protected function canEdit($appointmentDatetime, UserInterface $user, $attribute)
    {
        if ($this->checkAuthForXmlEdit($appointmentDatetime, $user, $attribute)) {
            return true;
        }

        if (!$this->locator->get('security')->isGranted('ROLE_CITIZEN') || !$this->checkAuth($appointmentDatetime,
                $user, $attribute)) {

            return false;
        }

        return true;
    }

    /**
     * @param                                                     $appointmentDatetime
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return bool|mixed
     */
    protected function canDelete($appointmentDatetime, UserInterface $user, $attribute)
    {
        if (!$this->locator->get('security')->isGranted('ROLE_CITIZEN') || !$this->checkAuth($appointmentDatetime,
                $user, $attribute)) {
            return false;
        }

        return true;
    }

    /**
     * @param                                                     $appointmentDatetimeClass
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return bool
     */
    protected function checkAuth($appointmentDatetimeClass, UserInterface $user, $attribute)
    {
        $request = $this->locator->get('request_stack')->getMasterRequest();
        $id      = $request->query->get('id');
        if (null === $id) {
            return false;
        }

        $entityManager = $this->locator->get('doctrine.orm.entity_manager');

        $appointment = $entityManager->getRepository(AppointmentDatetime::class)->find($id);
        if ($appointment->getCitizen() !== $user) {
            return false;
        }

        return true;
    }

    /**
     * @param                                                     $appointmentDatetime
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return bool
     */
    protected function checkAuthForXmlEdit($appointmentDatetime, UserInterface $user, $attribute)
    {
        $masterRequest = $this->locator->get('request_stack')->getMasterRequest();
        $appointmentDatetimeId = $masterRequest->query->get('id');
        $property       = $masterRequest->query->get('property');

        if (!$masterRequest->isXmlHttpRequest() || null === $appointmentDatetimeId || !in_array($property,
                self::ALLOWED_FIELDS_XML)) {
            return false;
        }

        $entityManager = $this->locator->get('doctrine.orm.entity_manager');
        $appointmentDatetimeDb   = $entityManager->getRepository(AppointmentDatetime::class)->find($appointmentDatetimeId);

        if (null === $appointmentDatetimeId || !($appointment = $appointmentDatetimeDb->getAppointment()) || $appointment->getLawyer() !== $user) {
            return false;
        }

        return true;
    }

}
