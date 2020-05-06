<?php
/**
 * @package App
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

namespace App\EventSubscriber\Form;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\EventSubscriber\Container;

/**
 * Class AppointmentDatetimeTypeSubscriber
 * @package App\EventSubscriber
 * @author  Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */
class AppointmentDatetimeTypeSubscriber extends Container implements EventSubscriberInterface
{
    /**
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSet',
        ];
    }

    public function onPreSet(FormEvent $event)
    {
        $form = $event->getForm();

        if ($this->isGranted('ROLE_CITIZEN')) {
            $form
                ->add('datetime', DateTimeType::class, [
                    'widget' => 'single_text',
                    'html5'  => false,
                    'attr'   => ['class' => 'js-datetimepicker'],
                    'label'  => 'form.datetime'
                ]);

        }

        if ($this->isGranted('ROLE_LAWYER')) {
            $form->add('accepted', null, ['label' => 'form.accepted']);
        }

    }

}
