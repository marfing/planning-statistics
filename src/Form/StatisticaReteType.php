<?php

namespace App\Form;

use App\Entity\StatisticaRete;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatisticaReteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('valore')
            ->add('data')
            ->add('nome_valore')
            ->add('elemento_rete')
            ->add('capacit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StatisticaRete::class,
        ]);
    }
}
