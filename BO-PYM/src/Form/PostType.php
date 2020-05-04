<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('published', DateTimeType::class, [
                'attr' => ['class' => 'reg rounded form-control'],
            ])
            ->add('updated', DateTimeType::class, [
                'attr' => ['class' => 'reg rounded form-control'],
            ])
            ->add('url', UrlType::class, [
                'attr' => ['class' => 'reg rounded form-control'],
            ])
            ->add('title', TextType::class, [
                'attr' => ['class' => 'reg rounded form-control'],
            ])
            ->add('content', TextType::class, [  // TODO : Change to editor
                'attr' => ['class' => 'reg rounded form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
