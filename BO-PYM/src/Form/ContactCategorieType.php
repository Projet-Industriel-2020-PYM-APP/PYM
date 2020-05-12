<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\ContactCategorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ContactCategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'row_attr' => [
                    'class' => 'row my-2'
                ],
                'label_attr' => [
                    'class' => 'h6'
                ],
                'attr' => [
                    'placeholder' => "Suggestion à propos du site",
                    'class' => 'reg rounded form-control'
                ],
                'label' => "Type de contact",
            ])
            ->add('subtitle', TextType::class, [
                'row_attr' => [
                    'class' => 'row my-2'
                ],
                'label_attr' => [
                    'class' => 'h6'
                ],
                'attr' => [
                    'placeholder' => "Envoyer des suggestions à l'aménageur urbain",
                    'class' => 'reg rounded form-control'
                ],
                'label' => "(optionnel) Sous-titre",
                'required' => false
            ])
            ->add('address', TextType::class, [
                'row_attr' => [
                    'class' => 'row my-2'
                ],
                'label_attr' => [
                    'class' => 'h6',
                ],
                'attr' => [
                    'placeholder' => "Boîte aux lettres",
                    'class' => 'reg rounded form-control'
                ],
                'label' => "(optionnel) Adresse",
                'required' => false
            ])
            ->add('imgUrl', FileType::class, [
                'label' => '(optionnel) Arrière plan de la catégorie (JPEG ou PNG)',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Image([
                        'mimeTypesMessage' => "Ce fichier n'est pas une image.",
                    ])
                ],
                'attr' => ['class' => 'custom-file-input'],
            ])
            ->add('actions', CollectionType::class, [
                'entry_type' => ActionType::class,
                'label' => 'Actions',
                'label_attr' => [
                    'class' => 'h6',
                ],
                'row_attr' => [
                    'class' => 'row my-2'
                ],
                'prototype' => true,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('contact', EntityType::class, [
                'class' => Contact::class,
                'label' => 'Contact',
                'label_attr' => [
                    'class' => 'h6'
                ],
                'row_attr' => [
                    'class' => 'row my-2'
                ],
                'attr' => [
                    'class' => 'reg rounded form-control'
                ],
                'choice_value' => 'id',
                'choice_label' => function ($category) {
                    return $category->getNom() . ' ' . $category->getPrenom();
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactCategorie::class,
        ]);
    }
}
