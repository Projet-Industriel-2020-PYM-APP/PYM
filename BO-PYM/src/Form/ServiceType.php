<?php


namespace App\Form;

use App\Entity\Service;
use App\Entity\ServiceCategorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ServiceType extends AbstractType
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
                    'placeholder' => "Restaurant",
                    'class' => 'reg rounded form-control'
                ],
                'label' => "Titre du service",
            ])
            ->add('subtitle', TextType::class, [
                'row_attr' => [
                    'class' => 'row my-2'
                ],
                'label_attr' => [
                    'class' => 'h6'
                ],
                'attr' => [
                    'placeholder' => "Le meilleur restaurant de Pôle Morandat !",
                    'class' => 'reg rounded form-control'
                ],
                'label' => "(optionnel) Sous-titre",
                'required' => false

            ])->add('imgUrl', FileType::class, [
                'label_attr' => [
                    'class' => 'h6'
                ],
                'mapped' => false,
                'constraints' => [
                    new Image([
                        'mimeTypesMessage' => "Ce fichier n'est pas une image.",
                    ])
                ],
                'label' => '(optionnel) Arrière plan du service (JPEG ou PNG)',
                'required' => false,
                'attr' => ['class' => 'custom-file-input']
            ])
            ->add('telephone', TextType::class, [
                'row_attr' => [
                    'class' => 'row my-2'
                ],
                'label_attr' => [
                    'class' => 'h6'
                ],
                'attr' => [
                    'placeholder' => "01 23 45 67 89",
                    'class' => 'reg rounded form-control'
                ],
                'label' => "(optionnel) Numéro de téléphone",
                'required' => false
            ])
            ->add('website', TextType::class, [
                'row_attr' => [
                    'class' => 'row my-2'
                ],
                'label_attr' => [
                    'class' => 'h6',
                ],
                'attr' => [
                    'placeholder' => "https://votresite.fr",
                    'class' => 'reg rounded form-control'
                ],
                'label' => "(optionnel) Lien Web",
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
                    'placeholder' => "Rue François",
                    'class' => 'reg rounded form-control'
                ],
                'label' => "(optionnel) Adresse",
                'required' => false
            ])->add('actions', CollectionType::class, [
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
            ])->add('categorie', EntityType::class, [
                'class' => ServiceCategorie::class,
                'label' => 'Catégorie',
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
                'choice_label' => 'name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
