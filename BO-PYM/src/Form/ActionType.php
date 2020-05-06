<?php


namespace App\Form;


use App\Entity\Action;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => "Nom de l'action [ex. : Réserver]",
                    'class' => 'reg form-control'
                ],
                'label' => false,
            ])
            ->add('htmlUrl', UrlType::class, [
                'attr' => [
                    'placeholder' => "(optionnel) Redirection [ex. : https://reservation.fr]",
                    'class' => 'reg form-control'
                ],
                'label' => false,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Action::class
        ]);
    }
}
