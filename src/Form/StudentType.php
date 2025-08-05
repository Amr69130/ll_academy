<?php

namespace App\Form;

use App\Entity\Student;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('adress')
            ->add('city')
            ->add('zipCode')
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('profilePicture', TextType::class, [
                'required' => false,
            ])
            ->add('level', ChoiceType::class, [
                'choices' => [
                    'A1 - Débutant' => 'A1',
                    'A2 - Élémentaire' => 'A2',
                    'B1 - Intermédiaire' => 'B1',
                    'B2 - Intermédiaire avancé' => 'B2',
                    'C1 - Avancé' => 'C1',
                    'C2 - Maîtrise' => 'C2',
                ],
                'placeholder' => 'Choisissez un niveau',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
