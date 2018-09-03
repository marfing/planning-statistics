<?php

namespace App\Form;

use App\Entity\FeasibilityB2B;
use App\Entity\User;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class FeasibilityB2BType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Type',ChoiceType::class, array(
                'choices' => array(
                    'b2b' => 'b2b',
                    'wholesale' => 'wholesale'
                ),
                'placeholder' => '',
                'label' => 'Tipo cliente',
            ))
            ->add('CustomerName', TextType::class, [
                'label' => 'Nome Cliente'
            ])
            ->add('TrunkType', ChoiceType::class, array(
                'choices' => array(
                    'Single' => 'single',
                    'Backup' => 'backup',
                    'Load Balance' => 'load balance',
                    'Trabocco' => 'trabocco'),
                'label' => 'Tipo di trunk',
            ))
            ->add('IPType', ChoiceType::class, array(
                'choices' => array(
                    'Public' => 'public',
                    'Private' => 'private'),
                'label' => 'Tipo di IP',
            ))
            ->add('CodecList', ChoiceType::class, array(
                'choices' => array(
                    'G.711 Alaw' => 'g.711alaw',
                    'G.729' => 'g.729'),
                'expanded' => 'true',
                'multiple' => 'false',
                'required' => true,
                'label' => 'Lista dei codec',
                'help' => 'Fare riferimento ai vincoli imposti da Ingegneria'
            ))
            ->add('MobilePercentage', IntegerType::class, [
                'label' => 'Percentuale traffico mobile',
                'help' => 'Da usare solo per fattibilità Wholesale'
            ])
            ->add('C2TChannels', IntegerType::class, [
                'label' => 'Numero canali (Customer to Tiscali)',
                'property_path' => 'Customer2TiscaliCapacity[Channels]',
            ])
            ->add('C2TMinutesPerMonth', IntegerType::class, [
                'label' => 'Numero minuti per mese (Customer to Tiscali)',
                'property_path' => 'Customer2TiscaliCapacity[MinutesPerMonth]',
            ])
            ->add('C2TErlang', IntegerType::class, [
                'label' => 'Numero Erlangs (Customer to Tiscali)',
                'property_path' => 'Customer2TiscaliCapacity[Erlang]',
            ])
            ->add('T2CChannels', IntegerType::class, [
                'label' => 'Numero canali (Tiscali to Customer)',
                'property_path' => 'Tiscali2CustomerCapacity[Channels]',
            ])
            ->add('T2CMinutesPerMonth', IntegerType::class, [
                'label' => 'Numero Minuti per mese (Tiscali to Customer)',
                'property_path' => 'Tiscali2CustomerCapacity[MinutesPerMonth]',
            ])
            ->add('T2CErlang', IntegerType::class, [
                'label' => 'Numero Erlangs (Tiscali to Customer)',
                'property_path' => 'Tiscali2CustomerCapacity[Erlang]',
            ])
            ->add('Note')
        ;
        if( in_array('ROLE_ADMIN',$options['role']) or in_array('ROLE_PLANNING',$options['role']) ){
            $builder
                ->add('Status', ChoiceType::class, array(
                    'choices' => array(
                        'New' => 'new',
                        'WorkInProgress' => 'workinprogress',
                        'Approved' => 'approved',
                        'Rejected' => 'rejected')))
            ;
        }        
        
        if( in_array('ROLE_ADMIN',$options['role']) ){
            $builder
                ->add('User', EntityType::class, array(
                        'class' => User::class,
                        'query_builder' => function (EntityRepository $er) {
                                                    $qb = $er->createQueryBuilder('u');
                                                    return $qb->where($qb->expr()->like('u.roles', '?1'))
                                                            ->setParameter('1','%ROLE_B2B%');
                        },  
                        'choice_label' => 'UserName'
                ))            
                ;
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $feasibility = $event->getData();
            $form = $event->getForm();
            if($feasibility->getType() == 'b2b'){
                $form->add('MobilePercentage', IntegerType::class, [
                    'label' => 'Percentuale traffico mobile',
                    'attr' => array('style' => 'display:none;',),
                    'help' => 'Da usare solo per fattibilità Wholesale',
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FeasibilityB2B::class,
            'validation_groups' => ['create'],
            'role' => ['ROLE_USER']
        ]);
    }
}
