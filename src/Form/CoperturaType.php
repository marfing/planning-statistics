<?php

namespace App\Form;

use App\Entity\Copertura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoperturaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idScala')
            ->add('regione')
            ->add('provincia')
            ->add('codiceComune')
            ->add('frazione')
            ->add('particellaTop')
            ->add('indirizzo')
            ->add('civico')
            ->add('scalaPalazzina')
            ->add('codiceVia')
            ->add('idBuilding')
            ->add('coordinateBuilding')
            ->add('pop')
            ->add('totaleUI')
            ->add('statoBuilding')
            ->add('statoScalaPalazzina')
            ->add('datatRFCIndicativa')
            ->add('dataRFCEffettiva')
            ->add('dataRFAIndicativa')
            ->add('dataRFAEffettiva')
            ->add('dataUltimaModificaRecord')
            ->add('dataUltimaModificaStatoBuilding')
            ->add('dataUltimaVariazioneStatoScalaPalazzina')
            ->add('comune')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Copertura::class,
        ]);
    }
}
