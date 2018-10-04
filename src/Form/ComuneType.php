<?php

namespace App\Form;

use App\Entity\Comune;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComuneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('denominazione')
            ->add('codiceComune')
            ->add('codiceCittaMetropolitana')
            ->add('denominazioneCittaMetropolitana')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comune::class,
        ]);
    }

    public function buildPathForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('denominazione')
            ->add('codiceComune')
            ->add('codiceCittaMetropolitana')
            ->add('denominazioneCittaMetropolitana')
        ;
    }

}
