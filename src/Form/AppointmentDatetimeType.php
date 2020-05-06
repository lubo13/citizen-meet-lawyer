<?php

namespace App\Form;

use App\Entity\AppointmentDatetime;
use App\EventSubscriber\Form\AppointmentDatetimeTypeSubscriber;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class AppointmentDatetimeType extends AbstractType implements ServiceSubscriberInterface
{
    private $locator;

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    public static function getSubscribedServices()
    {
        return [
            AppointmentDatetimeTypeSubscriber::class => AppointmentDatetimeTypeSubscriber::class,
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->locator->get(AppointmentDatetimeTypeSubscriber::class));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['label'] = false;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AppointmentDatetime::class,
        ]);
    }
}
