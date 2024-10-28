<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserinfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('addressName', TextType::class, [
            'required' => true,
            'attr' => ['class' => 'form-control'],
        ])
        ->add('address', TextType::class, [
            'required' => true,
            'attr' => ['class' => 'form-control'],
        ])
        ->add('city', TextType::class, [
            'required' => true,
            'attr' => ['class' => 'form-control'],
        ])
        ->add('country', TextType::class, [
            'required' => true,
            'attr' => ['class' => 'form-control'],
        ])
        ->add('zipCode', TextType::class, [
            'required' => true,
            'attr' => ['class' => 'form-control'],
        ]);    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserInfo::class,
        ]);
    }
}
