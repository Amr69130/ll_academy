<?php

namespace App\Form;

use App\Entity\Course;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du cours',
                'attr' => ['class' => 'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300', 'rows' => 4],
            ])
            ->add('level', TextType::class, [
                'label' => 'Niveau',
                'attr' => ['class' => 'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300'],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'attr' => ['class' => 'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300'],
            ])
            ->add('flagPicture', FileType::class, [
                'label' => 'Image du drapeau',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300'],
            ])
            ->add('isOpen', CheckboxType::class, [
                'label' => 'Cours ouvert',
                'required' => false,
                'attr' => ['class' => 'mr-2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
