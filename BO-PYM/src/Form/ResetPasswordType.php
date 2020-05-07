<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, [
                'row_attr' => [
                    'class' => 'form-group'
                ],
                'label_attr' => [
                    'class' => 'h5'
                ],
                'attr' => [
                    'placeholder' => "Mot de passe",
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Length(['min' => 8]),
                    new Regex([
                        'pattern' => "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!$%@#£€*?&]{8,}$/i",
                        'message' => "Il faut 1 chiffre et 1 lettre au minimum",
                    ])
                ],
                'label' => "Mot de passe",
            ])
            ->add('confirm_password', PasswordType::class, [
                'row_attr' => [
                    'class' => 'form-group'
                ],
                'label_attr' => [
                    'class' => 'h5'
                ],
                'attr' => [
                    'placeholder' => "Confirmer le mot de passe",
                    'class' => 'form-control'
                ],
                'label' => "Confirmer le mot de passe",
            ])
            ->add('_submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-danger'
                ],
                'label' => 'Changer de mot de passe'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
