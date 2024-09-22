<?php

namespace App\Form;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom de la catégorie', // Assurez-vous que le placeholder correspond également à votre modification
                ],
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Description de la catégorie'
                ]
            ])
            ->add('parent', EntityType::class, [
                'class' => Category::class,
                // 'query_builder' => function (EntityRepository $er) {
                //     return $er->createQueryBuilder('c')
                //         ->orderBy('c.root, c.lft', 'ASC'); // Ordonner par racine puis par lft (left)
                // },
                'query_builder' => function (CategoryRepository $er) {
                    return $er->getTreeOrderedPageCategoriesQueryBuilder();
                },
                'choice_label' => function ($category) {
                    return str_repeat('-', $category->getLevel()) . ' ' . $category->getTitle();
                },
                'group_by' => function ($category) {
                    return $category->getRoot();
                },
                'placeholder' => '-- Choisir dans la liste --', // Ajouter l'option par défaut
                'autocomplete' => true,
                'required' => false,
                'label' => 'Catégorie parente',
                'help' => 'Si pas de parent choisi la nouvelle catégorie sera placée à la racine',
            ])
            // submit 
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
