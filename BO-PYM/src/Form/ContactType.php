<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Poste;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Nom', TextType::class, [
                'attr' => [
                    'placeholder' => "Nom",
                    'class' => 'reg rounded form-control'
                ],
                'label' => ' '
            ])
            ->add('Prenom', TextType::class, ['attr' => ['placeholder' => "Prénom", 'class' => 'reg rounded form-control'], 'label' => ' '])
            ->add('Mail', EmailType::class, ['attr' => ['placeholder' => "Adresse e-mail", 'class' => 'reg rounded form-control'], 'label' => ' '])
            ->add('Telephone', TextType::class, ['attr' => ['placeholder' => "Numéro de téléphone", 'class' => 'reg reg-end rounded form-control'], 'label' => ' '])
            ->add('poste', EntityType::class, [
                'required' => false,
                'class' => Poste::class,
                'choice_label' => 'Nom',
                'mapped' => false,
                'label' => ' ',
                'placeholder' => 'Choisir un poste',
                'attr' => ['class' => 'reg reg-end rounded form-control']
            ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
