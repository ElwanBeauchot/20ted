<?php

namespace App\Form;

use App\Entity\SecurityUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormupdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Formulaire pour les informations utilisateur
        $builder
            ->add('username', TextType::class)
            ->add('email', EmailType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SecurityUser::class,
        ]);
    }
}