<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('content', TextareaType::class, [
            'label' => 'Votre commentaire',
            'required' => true,
        ])
        ->add('rating', ChoiceType::class, [
            'label' => 'Votre note',
            'choices' => [
                '⭐️' => 1,
                '⭐️⭐️' => 2,
                '⭐️⭐️⭐️' => 3,
                '⭐️⭐️⭐️⭐️' => 4,
                '⭐️⭐️⭐️⭐️⭐️' => 5,
            ],
            'expanded' => true, // Rendre les choix cliquables
            'multiple' => false, // Choix unique
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Ajouter un commentaire',
            'attr' => ['class' => 'btn btn-primary'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
