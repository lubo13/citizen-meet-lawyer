<?php
/**
 * @package Mysupply_App
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

namespace App\Security\Voter;

use App\Entity\Appointment;
use App\Entity\AppointmentInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AppointmentVoter
 * @package App\Security\Voter
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */
class AppointmentVoter extends AbstractEntityVoter
{
    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::CREATE, self::EDIT, self::LIST, self::DELETE, self::SHOW, self::FILTERS, self::SEARCH])) {
            return false;
        }

        if ($subject instanceof AppointmentInterface) {
            return true;
        } elseif ($subject === Appointment::class) {
            return true;
        } elseif ($subject instanceof \ReflectionClass && $subject->implementsInterface(AppointmentInterface::class)) {
            return true;
        }
        return false;
    }

    /**
     * @param                                                     $appointment
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    protected function canCreate($appointment, UserInterface $user, $attribute)
    {
        return $this->locator->get('security')->isGranted('ROLE_CITIZEN');
    }

    /**
     * @param                                                     $appointment
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    protected function canList($appointment, UserInterface $user, $attribute)
    {
        return true;
    }

    /**
     * @param                                                     $appointment
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    protected function canFilters($appointment, UserInterface $user, $attribute)
    {
        return true;
    }

    /**
     * @param                                                     $appointment
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    protected function canSearch($appointment, UserInterface $user, $attribute)
    {
        return true;
    }

    /**
     * @param                                                     $appointment
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return mixed
     */
    protected function canShow($appointment, UserInterface $user, $attribute)
    {
        return true;
    }

    /**
     * @param                                                     $appointment
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return bool|mixed
     */
    protected function canEdit($appointment, UserInterface $user, $attribute)
    {
        if (!$this->locator->get('security')->isGranted('ROLE_CITIZEN') || !$this->checkEditAuth($appointment,
                $user, $attribute)) {

            return false;
        }

        return true;
    }

    /**
     * @param                                                     $appointment
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return bool|mixed
     */
    protected function canDelete($appointment, UserInterface $user, $attribute)
    {
        if (!$this->locator->get('security')->isGranted('ROLE_CITIZEN') || !$this->checkDeleteAuth($appointment,
                $user, $attribute)) {
            return false;
        }

        return true;
    }

    /**
     * @param                                                     $appointmentClass
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return bool
     */
    protected function checkEditAuth($appointmentClass, UserInterface $user, $attribute)
    {
        $request = $this->locator->get('request_stack')->getMasterRequest();
        $id      = $request->query->get('id');
        if (null === $id) {
            return false;
        }

        $entityManager = $this->locator->get('doctrine.orm.entity_manager');

        $appointment = $entityManager->getRepository(Appointment::class)->find($id);
        if ($appointment->getCitizen() !== $user) {
            return false;
        }

        $auth = true;

        foreach ($appointment->getAppointmentDatetimes() as $appointmenDatetime) {
            if ($appointmenDatetime->getAccepted()) {
                return false;
            }
        }

        return $auth;
    }

    /**
     * @param                                                     $appointmentClass
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param                                                     $attribute
     *
     * @return bool
     */
    protected function checkDeleteAuth($appointmentClass, UserInterface $user, $attribute)
    {
        $request = $this->locator->get('request_stack')->getMasterRequest();
        $id      = $request->query->get('id');
        if (null === $id) {
            return false;
        }

        $entityManager = $this->locator->get('doctrine.orm.entity_manager');

        $appointment = $entityManager->getRepository(Appointment::class)->find($id);
        if ($appointment->getCitizen() !== $user) {
            return false;
        }

        $auth = true;
        foreach ($appointment->getAppointmentDatetimes() as $appointmenDatetime) {
            if ($appointmenDatetime->getAccepted() !== null) {
                return false;
            }
        }

        return $auth;
    }

}
