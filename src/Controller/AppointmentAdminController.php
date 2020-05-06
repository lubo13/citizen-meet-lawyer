<?php

namespace App\Controller;

use App\Entity\AppointmentInterface;
use App\Form\AppointmentType;
use App\Form\CustomEasyAdminFiltersFormType;
use App\Form\Filter\CustomEntityFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Form\FormInterface;

class AppointmentAdminController extends EasyAdminController
{
    protected function createFiltersForm(string $entityName): FormInterface
    {
        $this->entity['user_role'] = '';
        if ($this->isGranted('ROLE_CITIZEN')) {
            $this->entity['user_role'] = 'ROLE_CITIZEN';
        }
        if ($this->isGranted('ROLE_LAWYER')) {
            $this->entity['user_role'] = 'ROLE_LAWYER';
        }

        return $this->get('form.factory')->createNamed('filters', CustomEasyAdminFiltersFormType::class, null, [
            'method' => 'GET',
            'entity' => $this->entity,
        ]);
    }

    protected function createAppointmentNewForm(AppointmentInterface $appointment, $options)
    {
        return $this->get('form.factory')->createBuilder(AppointmentType::class, $appointment)->getForm();
    }

    protected function createAppointmentEditForm(AppointmentInterface $appointment, $options)
    {
        return $this->get('form.factory')->createBuilder(AppointmentType::class, $appointment)->getForm();
    }

    protected function renderAppointmentTemplate($actionName, $templatePath, array $parameters = [])
    {
        switch ($actionName) {
            case 'list':
                $templatePath = 'CRUD/appointment_list.html.twig';
                break;
            case 'new':
                $templatePath = 'CRUD/appointment_new.html.twig';
                break;
            case 'show':
                $templatePath = 'CRUD/appointment_show.html.twig';
                break;
            case 'search':
                $templatePath = 'CRUD/appointment_list.html.twig';
                break;
        }

        return $this->render($templatePath, $parameters);
    }

}
