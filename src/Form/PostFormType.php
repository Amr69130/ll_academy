<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\PostType as PostTypeEntity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('content', null, [
                'label' => 'Contenu',
            ])
            ->add('image', TextType::class, [
                'label' => 'Image',
                'required' => false, // au cas où l’image est optionnelle
            ])
            ->add('created_at', DateTimeType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',   // input HTML5 (date-heure)
                'html5' => true,
                'required' => false,
            ])
            ->add('type', EntityType::class, [
                'class' => PostTypeEntity::class,
                'choice_label' => 'type',
                'label' => 'Type de post',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
