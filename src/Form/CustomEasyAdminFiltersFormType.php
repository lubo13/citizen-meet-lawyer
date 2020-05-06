<?php

namespace App\Form;

use App\Form\Filter\CustomEntityFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Configurator\TypeConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminFiltersFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomEasyAdminFiltersFormType extends EasyAdminFiltersFormType
{
    private $configManager;

    private $configurators;

    /**
     * @param TypeConfiguratorInterface[] $configurators
     */
    public function __construct(ConfigManager $configManager, array $configurators = [])
    {
        $this->configManager = $configManager;
        $this->configurators = $configurators;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $passedEntityConfig = $options['entity'];

        foreach ($passedEntityConfig['list']['filters'] as $propertyName => $filterConfig) {
            if (($passedEntityConfig['user_role'] == 'ROLE_CITIZEN' && $propertyName == 'citizen') || ($passedEntityConfig['user_role'] == 'ROLE_LAWYER' && $propertyName == 'lawyer')) {
                continue;
            }

            $formFieldOptions = $filterConfig['type_options'];

            // Configure options using the list of registered type configurators:
            foreach ($this->configurators as $configurator) {
                if ($configurator->supports($filterConfig['type'], $formFieldOptions, $filterConfig)) {
                    $formFieldOptions = $configurator->configure($propertyName, $formFieldOptions, $filterConfig,
                        $builder);
                }
            }

            $builder->add($propertyName, $filterConfig['type'], $formFieldOptions);

        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('entity');
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'csrf_protection'    => false,
        ]);
        $resolver->setAllowedTypes('entity', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'easyadmin_filters';
    }
}
