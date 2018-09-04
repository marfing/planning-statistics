<?php

namespace App\Form;

use App\Entity\TrafficReport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrafficReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('routerInName')
            ->add('routerInIP')
            ->add('routerOutName')
            ->add('routerOutIP')
            ->add('bandwidth')
            ->add('lastTimestamp')
            ->add('routerIn')
            ->add('routerOut')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrafficReport::class,
        ]);
    }
}
