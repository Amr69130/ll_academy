<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Enrollment;
use App\Entity\EnrollmentPeriod;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EnrollmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enrollmentPeriod', EntityType::class, [
                'class' => EnrollmentPeriod::class,
                'choice_label' => 'title',
                'label' => 'Période d\'inscription',
                'query_builder' => fn($er) => $er->createQueryBuilder('e')
                    ->where('e.isOpen = true'),
                'disabled' => $options['lock_period'], // ⬅️ ICI
            ])
            ->add('course', EntityType::class, [
                'class' => Course::class,
                'choice_label' => fn($course) => $course->getName() . ' (' . $course->getLevel() . ')',
                'label' => 'Cours',
                'query_builder' => fn($er) => $er->createQueryBuilder('c')
                    ->where('c.isOpen = true'),
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Inscrire l\'élève',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enrollment::class,
            'lock_period' => false, // ⬅️ défaut
        ]);
        $resolver->setAllowedTypes('lock_period', 'bool');
    }
}