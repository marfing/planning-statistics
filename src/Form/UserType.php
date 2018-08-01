<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if( in_array('ROLE_ADMIN',$options['role'])){
            $builder
            ->add('password',PasswordType::class)
            ->add('username')
            ->add('email',EmailType::class)
            ->add('roles', ChoiceType::class, array(
                    'choices' => array(
                        'Admin' => 'ROLE_ADMIN',
                        'Planner' => 'ROLE_PLANNING',
                        'User' => 'ROLE_USER',
                        'B2B' => 'ROLE_B2B',
                        'GUEST' => 'ROLE_GUEST',
                    ),
                'expanded' => false,
                'multiple' => true
            ))
            ->add('isActive');
        } else {
            $builder
            ->add('username',TextType::class, array(
                'disabled' => true
            ))
            ->add('password',PasswordType::class)
            ->add('email',EmailType::class, array(
                'disabled' => true
            ));
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
