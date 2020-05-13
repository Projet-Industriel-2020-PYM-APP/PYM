<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => "Adresse email",
                    'class' => 'reg-email rounded form-control'],
                'label' => 'Adresse e-mail',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes doivent correspondre.',
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Mot de passe',
                        'class' => 'password-field reg-email rounded form-control',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmer le mot de passe',
                        'class' => 'password-field reg-email rounded form-control',
                    ]
                ],
                'constraints' => [
                    new Length(['min' => 8]),
                    new Regex([
                        'pattern' => "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!$%@#£€*?&]{8,}$/i",
                        'message' => "Il faut 1 chiffre et 1 lettre au minimum",
                    ])
                ],
            ]);
            //->add('username', TextType::class,['attr' => ['placeholder' => "Identifiant", 'class' => 'reg-username rounded form-control'] , 'label' => ' '])
            //->add('role',
            //    ChoiceType::class,
            //    [
            //        'choices' => ['Admin' => "Admin", 'User' => "User"],
            //        'label' => ' ',
            //        'attr' => ['class' => 'role rounded rounded']
            //    ]
            //);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
