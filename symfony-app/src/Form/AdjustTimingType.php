<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class AdjustTimingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('distance', IntegerType::class, [
                'label' => 'Distancia recorrida (m)',
            ])
            ->add('current_time', DateTimeType::class, [
                'label' => 'Hora actual',
                'widget' => 'single_text',
                'with_seconds' => true,
            ]);
    }
}