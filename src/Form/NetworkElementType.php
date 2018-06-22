<?php

namespace App\Form;

use App\Entity\NetworkElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class NetworkElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nome', TextType::class, array(
            'label' => 'Nome dell\'elemento di rete'
            ))
        ->add('capacity', IntegerType::class, array(
                'label' => 'Max capacity of the element'
            ))
            ->add('csvCapacityTypeName', TextType::class, array(
                'label' => 'Name of the capacity value in ZTE CSV file',
            ))
            ->add('capacityType', ChoiceType::class, array(
                'choices' => array(
                    'Max Calls' => 'calls',
                    'Max Users' => 'users',
                    'Max Voice Mail' => 'voicemail'
                ),
                'attr' => array(
                    'class' => 'form-control'))) 
            ->add('directoryStatistiche', TextType::class, array(
                'label' => 'Directory dove si trova il file CSV',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NetworkElement::class,
        ]);
    }
}
