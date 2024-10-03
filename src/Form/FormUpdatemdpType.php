<?php

namespace App\Form;

use App\Entity\SecurityUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class FormUpdatemdpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Formulaire pour le mot de passe
        $builder
            ->add('password', PasswordType::class, [
                'required' => false, // Le mot de passe est facultatif
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SecurityUser::class,
        ]);
    }
}
