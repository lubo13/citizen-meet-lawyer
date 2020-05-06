<?php
/**
 * @package App
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

namespace App\EventSubscriber\Form;

use App\Entity\Appointment;
use App\Entity\AppointmentDatetime;
use App\Entity\User;
use App\Form\AppointmentDatetimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\EventSubscriber\Container;

/**
 * Class AppointmentTypeSubscriber
 * @package App\EventSubscriber
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */
class AppointmentTypeSubscriber extends Container implements EventSubscriberInterface
{
    /**
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSet',
            FormEvents::POST_SUBMIT  => 'onPostSubmit',
        ];
    }

    public function onPreSet(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $form
            ->add('lawyer', EntityType::class,
                [
                    'label'         => 'form.lawyer',
                    'class'         => User::class,
                    'query_builder' => function ($repository) {
                        $qb = $repository->createQueryBuilder('u');
                        $qb->andWhere('u.roles LIKE :role')
                            ->setParameter('role', '%ROLE_LAWYER%')
                            ->orderBy('u.id', 'DESC');
                        return $qb;
                    },
                    'attr'          => ['class' => 'form-group']
                ]
            )
            ->add('appointmentDatetimes', CollectionType::class,
                [
                    'entry_type'   => AppointmentDatetimeType::class,
                    'label'        => 'form.appointment_datetimes',
                    'by_reference' => false
                ]
            );


        if ($this->isGranted('ROLE_CITIZEN')) {
            $addAppointmentDatetime = true;
            foreach ($data->getAppointmentDatetimes() as $appointmentDatetime) {
                if ($appointmentDatetime->getAccepted() !== false) {
                    $addAppointmentDatetime = false;
                }
            }

            if ($addAppointmentDatetime) {
                $data->clearAppointmentDatetimes();
                $data->addAppointmentDatetime(new AppointmentDatetime());
            } else {
                $appointmentDatetimes = $data->getAppointmentDatetimes();
                $data->clearAppointmentDatetimes();
                $data->addAppointmentDatetime($appointmentDatetimes->get($appointmentDatetimes->count() - 1));
            }
        }

        $form->add('submit', SubmitType::class, ['attr' => ['class' => 'btn-primary m-auto']]);
    }

    public function onPostSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        if ($data->getLawyer()) {
            $em                 = $this->locator->get('doctrine.orm.entity_manager');
            $lawyerAppointments = $em->getRepository(Appointment::class)->findBy(['lawyer' => $data->getLawyer()->getId()]);
            if ($submitedAppointment = $data->getAppointmentDatetimes()) {
                $lawyerBusyMsg = $this->locator->get('translator')->trans('lawyer_is_busy');
                $count         = 0;
                foreach ($lawyerAppointments as $lawyerAppointment) {
                    foreach ($lawyerAppointment->getAppointmentDatetimes() as $lawyerAppointmentDt) {
                        if ($lawyerAppointmentDt->getDatetime()->getTimestamp() === $submitedAppointment[0]->getDatetime()->getTimestamp() && $lawyerAppointmentDt->getAccepted() !== false && $lawyerAppointmentDt->getId() !== $submitedAppointment[0]->getId()) {
                            $count++;
                        }
                    }
                }

                if ($count > 0) {
                    $form->get('appointmentDatetimes')->addError(new FormError($lawyerBusyMsg));
                }
            }
        }
    }

}
