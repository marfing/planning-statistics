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
                'placeholder' => ''
            ))
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
                'multiple' => 'false',
                'required' => true))
            ->add('C2TChannels', IntegerType::class, [
                'label' => 'Incomig (Customer 2 Tiscali) channels',
                'property_path' => 'Customer2TiscaliCapacity[Channels]',
                'data' => 0
            ])
            ->add('C2TMinutesPerMonth', IntegerType::class, [
                'label' => 'Incomig (Customer 2 Tiscali) minutes per month',
                'property_path' => 'Customer2TiscaliCapacity[MinutesPerMonth]',
                'data' => 0
            ])
            ->add('C2TErlang', IntegerType::class, [
                'label' => 'Incomig (Customer 2 Tiscali) erlangs',
                'property_path' => 'Customer2TiscaliCapacity[Erlang]',
                'data' => 0
            ])
            ->add('T2CChannels', IntegerType::class, [
                'label' => 'Outgoing (Tiscali 2 Customer) channels',
                'property_path' => 'Tiscali2CustomerCapacity[Channels]',
                'data' => 0
            ])
            ->add('T2CMinutesPerMonth', IntegerType::class, [
                'label' => 'Outgoing (Tiscali 2 Customer) minutes per month',
                'property_path' => 'Tiscali2CustomerCapacity[MinutesPerMonth]',
                'data' => 0
            ])
            ->add('T2CErlang', IntegerType::class, [
                'label' => 'Outgoing (Tiscali 2 Customer) erlangs',
                'property_path' => 'Tiscali2CustomerCapacity[Erlang]',
                'data' => 0
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

        $formModifier = function (FormInterface $form, $type = null) {
            if($type == 'wholesale'){
                $form->add('MobilePercentage'); 
            }
        };    

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                $typeValue = $data->getType();
                if($typeValue == "wholesale"){
                    $form->add('MobilePercentage');
                }
            }
        );

        $builder->get('Type')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $type = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $type);
            }
        );
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
