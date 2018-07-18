<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password',PasswordType::class)
            ->add('email',EmailType::class);

        if( in_array('ROLE_ADMIN',$options['role'])){
            $builder
                ->add('roles', ChoiceType::class, array(
                    'choices' => array(
                        'Admin' => 'ROLE_ADMIN',
                        'Planner' => 'ROLE_PLANNING',
                        'User' => 'ROLE_USER',
                        'B2B' => 'ROLE_B2B'
                    ),
                    'expanded' => false,
                    'multiple' => true
                ))
                ->add('isActive');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['create'],
            'role' => ['ROLE_USER']
        ]);
    }
}
