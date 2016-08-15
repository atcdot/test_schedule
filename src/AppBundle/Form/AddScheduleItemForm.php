<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddScheduleItemForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('region', NULL, [
            'label'    => 'Регион',
            'required' => TRUE,
            'attr'     => [
                'id' => 'regionSelect',
            ],
        ])
            ->add('departureDate', DateTimeType::class, [
                'widget'   => 'single_text',
                'html5'    => FALSE,
                'required' => TRUE,
                'label'    => 'Дата выезда из Москвы',
            ])
            ->add('courier', NULL, [
                'label'    => 'Курьер',
                'required' => TRUE,
                'attr'     => [
                    'id' => 'courierSelect',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Schedule',
        ]);
    }
}