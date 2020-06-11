<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\DataTransformer\ServiceToIDTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\BooleanToStringTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingAPIType extends AbstractType
{
    private $serviceTransformer;
    private $dateTimeTransformer;

    public function __construct(ServiceToIDTransformer $serviceToIDTransformer)
    {
        $this->serviceTransformer = $serviceToIDTransformer;
        $this->dateTimeTransformer = new DateTimeToStringTransformer('Europe/Paris', "UTC", "Y-m-d\TH:i:s.u\Z");
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class)
            ->add('start_date', TextType::class, [
                'property_path' => 'startDate',
                'invalid_message' => '"start_date" est erroné.',
            ])
            ->add('end_date', TextType::class, [
                'property_path' => 'endDate',
                'invalid_message' => '"end_date" est erroné.',
            ])
            ->add('service_id', TextType::class, [
                'property_path' => 'service',
                'invalid_message' => '"service_id" est erroné.',
            ])
            ->add('superpose');

        $builder->get('service_id')->addModelTransformer($this->serviceTransformer);
        $builder->get('start_date')->addModelTransformer($this->dateTimeTransformer);
        $builder->get('end_date')->addModelTransformer($this->dateTimeTransformer);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'csrf_protection' => false,
        ]);
    }
}
