<?php


namespace App\Form;


use App\Entity\ServiceCategorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ServiceCategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la catégorie',
                'row_attr' => [
                  'class' => 'form-group'
                ],
                'attr' => [
                    'class' => 'reg form-control',
                    'placeholder' => "Catégorie",
                ],
            ])
            ->add('primaryColor', ColorType::class, [
                'label' => "(optionnel) Couleur de la catégorie",
                'required' => false,
                'row_attr' => [
                    'class' => 'form-group'
                ],
                'attr' => [
                    'class' => 'reg form-control',
                ],
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
                'attr' => [ 'class' => 'custom-file-input' ],
            ])
            ->add('action', ActionType::class, [
                'required' => true,
                'label_attr' => [ 'class' => 'col-sm-2' ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ServiceCategorie::class
        ]);
    }
}
