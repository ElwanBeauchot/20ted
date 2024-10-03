<?php

namespace App\Form;

use App\Entity\SecurityUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class WalletformType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', NumberType::class, [
                'label' => 'Montant',
                'required' => true,
            ])
            ->add('operation', ChoiceType::class, [
                'label' => 'Opération',
                'choices' => [
                    'Ajouter' => 'add',
                    'Retirer' => 'subtract',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
