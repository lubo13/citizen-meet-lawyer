<?php

namespace App\Doctrine\Filter;

use App\Entity\AppointmentDatetimeInterface;
use App\Entity\CitizenInterface;
use App\Entity\LawyerInterface;
use Doctrine\ORM\Mapping\ClassMetadata,
    Doctrine\ORM\Query\Filter\SQLFilter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of UserFilter
 *
 * @author Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */
class UserFilter extends SQLFilter
{

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->reflClass->implementsInterface(LawyerInterface::class) && $this->hasParameter('lawyer_id')) {
            return $targetTableAlias . '.lawyer_id = ' . $this->getParameter('lawyer_id');
        } elseif ($targetEntity->reflClass->implementsInterface(CitizenInterface::class) && $this->hasParameter('citizen_id')) {
            return $targetTableAlias . '.citizen_id = ' . $this->getParameter('citizen_id');
        }

        if ($targetEntity->reflClass->implementsInterface(AppointmentDatetimeInterface::class) && $this->hasParameter('lawyer_id')) {
            return $targetTableAlias . '.id IN (SELECT ad.id FROM `appointment_datetime` AS ad JOIN `appointment` AS aa WHERE ad.`appointment_id`=aa.`id` AND aa.lawyer_id =' . $this->getParameter('lawyer_id') . ')';
        } elseif ($targetEntity->reflClass->implementsInterface(AppointmentDatetimeInterface::class) && $this->hasParameter('citizen_id')) {
            return $targetTableAlias . '.id IN (SELECT ad.id FROM `appointment_datetime` AS ad JOIN `appointment` AS aa WHERE ad.`appointment_id`=aa.`id` AND aa.citizen_id =' . $this->getParameter('citizen_id') . ')';
        }

        if ($targetEntity->reflClass->implementsInterface(UserInterface::class) && $this->hasParameter('lawyer_id')) {
            return $targetTableAlias . '.roles LIKE "%ROLE_CITIZEN%"';
        } elseif ($targetEntity->reflClass->implementsInterface(UserInterface::class) && $this->hasParameter('citizen_id')) {
            return $targetTableAlias . '.roles LIKE "%ROLE_LAWYER%"';
        }

        return '';
    }

}
