<?php

namespace App\Form;

use App\Entity\FeasibilityB2B;
use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;



class FeasibilityB2BType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('CustomerName')
            ->add('TrunkType', ChoiceType::class, array(
                'choices' => array(
                    'Single' => 'single',
                    'Backup' => 'backup',
                    'Load Balance' => 'load balance',
                    'Trabocco' => 'trabocco'),
            ))
            ->add('IPType', ChoiceType::class, array(
                'choices' => array(
                    'Public' => 'public',
                    'Private' => 'private'),
            ))
            ->add('CodecList', ChoiceType::class, array(
                'choices' => array(
                    'G.711 Alaw' => 'g.711alaw',
                    'G.729' => 'g.729'),
                'expanded' => 'true',
                'multiple' => 'false'))
            ->add('C2TChannels', TextType::class, [
                'label' => 'Incomig (Customer 2 Tiscali) channels',
                'property_path' => 'Customer2TiscaliCapacity[Channels]',
                'data' => '0'
            ])
            ->add('C2TMinutesPerMonth', TextType::class, [
                'label' => 'Incomig (Customer 2 Tiscali) minutes per month',
                'property_path' => 'Customer2TiscaliCapacity[MinutesPerMonth]',
                'data' => '0'
            ])
            ->add('C2TErlang', TextType::class, [
                'label' => 'Incomig (Customer 2 Tiscali) erlangs',
                'property_path' => 'Customer2TiscaliCapacity[Erlang]',
                'data' => '0'
            ])
            ->add('T2CChannels', TextType::class, [
                'label' => 'Outgoing (Tiscali 2 Customer) channels',
                'property_path' => 'Tiscali2CustomerCapacity[Channels]',
                'data' => '0'
            ])
            ->add('T2CMinutesPerMonth', TextType::class, [
                'label' => 'Outgoing (Tiscali 2 Customer) minutes per month',
                'property_path' => 'Tiscali2CustomerCapacity[MinutesPerMonth]',
                'data' => '0'
            ])
            ->add('T2CErlang', TextType::class, [
                'label' => 'Outgoing (Tiscali 2 Customer) erlangs',
                'property_path' => 'Tiscali2CustomerCapacity[Erlang]',
                'data' => '0'
            ])
            ->add('Note')
        ;
        if( in_array('ROLE_ADMIN',$options['role']) or in_array('ROLE_PLANNING',$options['role']) ){
            $builder
                ->add('Status', ChoiceType::class, array(
                    'choices' => array(
                        'New' => 'new',
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
