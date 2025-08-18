<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('billing_address', TextType::class, [
                "label" => "Adresse",
            ])
            ->add('billing_city', TextType::class, [
                "label" => "Ville",
            ])
            ->add('billing_zip_code', NumberType::class, [
                "label" => 'Code postal',
                'attr' => [
                    'inputmode' => 'numeric'
                ]
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'inputmode' => 'tel'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
