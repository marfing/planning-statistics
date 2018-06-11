<?php

namespace App\Form;

use App\Entity\NetworkElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class NetworkElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nome')
            ->add('capacity')
            ->add('capacityType', ChoiceType::class, array(
                'choices' => array(
                    'Max Calls' => 'calls',
                    'Max Users' => 'users'),
                'attr' => array(
                    'class' => 'form-control')))
            ->add('directoryStatistiche')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NetworkElement::class,
        ]);
    }
}
