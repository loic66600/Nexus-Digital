<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('lastName', null, [
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('firstName', null, [
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('phone', null, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]);
           
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}