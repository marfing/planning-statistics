<?php

namespace App\Form;

use App\Entity\StatisticaRete;
use App\Entity\NetworkElement;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class StatisticaReteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
 //           ->add('valore')
 //           ->add('data')
 //           ->add('nome_valore')
            ->add('valore', IntegerType::class, array(
                'attr' => array('class' => 'form-control'),
                'required' => true))
            ->add('data', DateType::class, array(
                'widget' => 'choice',
                'attr' => array(
                    'class' => 'form-control')))
            ->add('networkElement', EntityType::class, array(
                'class' => NetworkElement::class,
                'choice_label' => 'Nome'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StatisticaRete::class,
        ]);
    }
}
